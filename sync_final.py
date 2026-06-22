import pandas as pd
import pymysql
import psycopg2
from sqlalchemy import create_engine
from datetime import datetime

# Koneksi ke MySQL (untuk membaca dataframe)
mysql_engine = create_engine('mysql+pymysql://root:@localhost:3306/oltp_rental')
pg_engine = create_engine('postgresql://postgres:0987654321@localhost:5432/dw_outdoor')

# Ambil transaksi yang belum sinkron
transaksi_df = pd.read_sql("SELECT * FROM transaksi WHERE synced = 0", mysql_engine)

if transaksi_df.empty:
    print("Tidak ada transaksi baru.")
    exit(0)

print(f"Ditemukan {len(transaksi_df)} transaksi baru. Memulai sinkronisasi...")

# Koneksi manual untuk eksekusi query (agar kompatibel dengan SQLAlchemy 2.0)
mysql_conn = pymysql.connect(host='localhost', user='root', password='', db='oltp_rental', charset='utf8mb4')
pg_conn = psycopg2.connect(host='localhost', database='dw_outdoor', user='postgres', password='0987654321', port=5432)
pg_cursor = pg_conn.cursor()
mysql_cursor = mysql_conn.cursor()

# Ambil mapping dari PostgreSQL
cabang_pg = pd.read_sql("SELECT id_cabang, kode_cabang FROM dim_cabang", pg_engine)
map_cabang = dict(zip(cabang_pg['kode_cabang'], cabang_pg['id_cabang']))

user_pg = pd.read_sql("SELECT id_user, kode_user FROM dim_user", pg_engine)
map_user = dict(zip(user_pg['kode_user'], user_pg['id_user']))

produk_pg = pd.read_sql("SELECT id_produk, kode_produk, valid_from FROM dim_produk WHERE is_current = true", pg_engine)
map_produk = {row['kode_produk']: (row['id_produk'], row['valid_from']) for _, row in produk_pg.iterrows()}

waktu_pg = pd.read_sql("SELECT id_waktu, tanggal FROM dim_waktu", pg_engine)
map_waktu = dict(zip(waktu_pg['tanggal'], waktu_pg['id_waktu']))

# Mapping dari MySQL
cabang_mysql = pd.read_sql("SELECT id_cabang, kode_cabang FROM cabang", mysql_engine)
map_cabang_mysql = dict(zip(cabang_mysql['id_cabang'], cabang_mysql['kode_cabang']))

user_mysql = pd.read_sql("SELECT id_user, username FROM users", mysql_engine)
map_user_mysql = dict(zip(user_mysql['id_user'], user_mysql['username']))

produk_mysql = pd.read_sql("SELECT id_produk, kode_produk FROM produk", mysql_engine)
map_produk_mysql = dict(zip(produk_mysql['id_produk'], produk_mysql['kode_produk']))

success = 0
for idx, row in transaksi_df.iterrows():
    try:
        # 1. Waktu
        tgl = row['tanggal']
        if tgl in map_waktu:
            id_waktu = map_waktu[tgl]
        else:
            tgl_obj = datetime.strptime(str(tgl), '%Y-%m-%d')
            pg_cursor.execute("""
                INSERT INTO dim_waktu (tanggal, hari, bulan, tahun, kuartal, nama_hari)
                VALUES (%s, %s, %s, %s, %s, %s) RETURNING id_waktu
            """, (tgl, tgl_obj.day, tgl_obj.month, tgl_obj.year, tgl_obj.quarter, tgl_obj.strftime("%A")))
            id_waktu = pg_cursor.fetchone()[0]
            pg_conn.commit()
            map_waktu[tgl] = id_waktu

        # 2. Cabang
        kode_cabang = map_cabang_mysql.get(row['cabang_id'])
        if not kode_cabang:
            print(f"  Cabang ID {row['cabang_id']} tidak ditemukan di MySQL, skip")
            continue
        id_cabang = map_cabang.get(kode_cabang)
        if not id_cabang:
            print(f"  Kode cabang {kode_cabang} tidak ditemukan di PostgreSQL, skip")
            continue

        # 3. User
        username = map_user_mysql.get(row['user_id'])
        if not username:
            print(f"  User ID {row['user_id']} tidak ditemukan di MySQL, skip")
            continue
        id_user = map_user.get(username)
        if not id_user:
            print(f"  Username {username} tidak ditemukan di PostgreSQL, skip")
            continue

        # 4. Produk
        kode_produk = map_produk_mysql.get(row['produk_id'])
        if not kode_produk:
            print(f"  Produk ID {row['produk_id']} tidak ditemukan di MySQL, skip")
            continue
        prod_info = map_produk.get(kode_produk)
        if not prod_info:
            print(f"  Kode produk {kode_produk} tidak ditemukan di PostgreSQL, skip")
            continue
        id_produk, valid_from = prod_info

        # 5. Cek duplikat
        pg_cursor.execute("SELECT 1 FROM fact_persewaan WHERE id_transaksi = %s", (row['id_transaksi'],))
        if pg_cursor.fetchone():
            print(f"  Transaksi {row['id_transaksi']} sudah ada, skip")
            mysql_cursor.execute("UPDATE transaksi SET synced = 1 WHERE id_transaksi = %s", (row['id_transaksi'],))
            mysql_conn.commit()
            success += 1
            continue

        # 6. Insert ke fact_persewaan
        pg_cursor.execute("""
            INSERT INTO fact_persewaan (id_transaksi, id_waktu_fk, id_cabang_fk, id_user_fk, id_produk_fk, valid_from_produk, jumlah_unit, total_harga_sewa, total_denda)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)
        """, (row['id_transaksi'], id_waktu, id_cabang, id_user, id_produk, valid_from, row['jumlah'], row['total_harga'], row['denda']))
        pg_conn.commit()

        # 7. Update synced = 1 di MySQL
        mysql_cursor.execute("UPDATE transaksi SET synced = 1 WHERE id_transaksi = %s", (row['id_transaksi'],))
        mysql_conn.commit()
        success += 1
        print(f"  Transaksi {row['id_transaksi']} berhasil disinkron.")
    except Exception as e:
        print(f"  Gagal sinkron transaksi {row['id_transaksi']}: {e}")
        pg_conn.rollback()
        mysql_conn.rollback()

print(f"Selesai. {success} dari {len(transaksi_df)} transaksi berhasil disinkron.")
pg_cursor.close()
pg_conn.close()
mysql_cursor.close()
mysql_conn.close()