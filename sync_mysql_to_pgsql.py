import pandas as pd
from sqlalchemy import create_engine, text
from datetime import datetime

# Koneksi ke MySQL (OLTP) menggunakan pymysql (pastikan sudah diinstall: pip install pymysql)
mysql_engine = create_engine('mysql+pymysql://root:@localhost:3306/oltp_rental')

# Koneksi ke PostgreSQL (Data Warehouse)
pg_engine = create_engine('postgresql://postgres:0987654321@localhost:5432/dw_outdoor')

# Ambil transaksi yang belum disinkron (synced = 0)
transaksi_df = pd.read_sql("SELECT * FROM transaksi WHERE synced = 0", mysql_engine)

if transaksi_df.empty:
    print("Tidak ada transaksi baru.")
    exit(0)

print(f"Ditemukan {len(transaksi_df)} transaksi baru. Memulai sinkronisasi...")

# Ambil data mapping dari PostgreSQL
cabang_pg = pd.read_sql("SELECT id_cabang, kode_cabang, nama_kota FROM dim_cabang", pg_engine)
user_pg = pd.read_sql("SELECT id_user, kode_user FROM dim_user", pg_engine)
produk_pg = pd.read_sql("SELECT id_produk, kode_produk, valid_from FROM dim_produk WHERE is_current = true", pg_engine)
waktu_pg = pd.read_sql("SELECT id_waktu, tanggal FROM dim_waktu", pg_engine)

# Mapping helper
cabang_map = dict(zip(cabang_pg['kode_cabang'], cabang_pg['id_cabang']))
user_map = dict(zip(user_pg['kode_user'], user_pg['id_user']))
produk_map = {}
for _, row in produk_pg.iterrows():
    produk_map[row['kode_produk']] = {'id_produk': row['id_produk'], 'valid_from': row['valid_from']}

# Ambil data dari MySQL untuk mapping foreign key
cabang_mysql = pd.read_sql("SELECT id_cabang, kode_cabang FROM cabang", mysql_engine)
cabang_mysql_map = dict(zip(cabang_mysql['id_cabang'], cabang_mysql['kode_cabang']))

user_mysql = pd.read_sql("SELECT id_user, username FROM users", mysql_engine)
user_mysql_map = dict(zip(user_mysql['id_user'], user_mysql['username']))

produk_mysql = pd.read_sql("SELECT id_produk, kode_produk FROM produk", mysql_engine)
produk_mysql_map = dict(zip(produk_mysql['id_produk'], produk_mysql['kode_produk']))

success = 0
for idx, row in transaksi_df.iterrows():
    try:
        # 1. Cari atau buat id_waktu
        tgl = row['tanggal']
        id_waktu = waktu_pg[waktu_pg['tanggal'] == tgl]
        if id_waktu.empty:
            # Insert ke dim_waktu jika belum ada
            tgl_obj = datetime.strptime(str(tgl), '%Y-%m-%d')
            insert_waktu = text(f"""
                INSERT INTO dim_waktu (tanggal, hari, bulan, tahun, kuartal, nama_hari)
                VALUES ('{tgl}', {tgl_obj.day}, {tgl_obj.month}, {tgl_obj.year}, {tgl_obj.quarter}, '{tgl_obj.strftime("%A")}')
                RETURNING id_waktu
            """)
            id_waktu = pg_engine.execute(insert_waktu).fetchone()[0]
        else:
            id_waktu = id_waktu.iloc[0]['id_waktu']

        # 2. Mapping cabang
        kode_cabang = cabang_mysql_map.get(row['cabang_id'])
        if kode_cabang is None:
            print(f"  Cabang ID {row['cabang_id']} tidak ditemukan di MySQL, skip")
            continue
        id_cabang = cabang_map.get(kode_cabang)
        if id_cabang is None:
            print(f"  Cabang dengan kode {kode_cabang} tidak ditemukan di PostgreSQL, skip")
            continue

        # 3. Mapping user
        username = user_mysql_map.get(row['user_id'])
        if username is None:
            print(f"  User ID {row['user_id']} tidak ditemukan di MySQL, skip")
            continue
        id_user = user_map.get(username)
        if id_user is None:
            print(f"  User {username} tidak ditemukan di PostgreSQL, skip")
            continue

        # 4. Mapping produk
        kode_produk = produk_mysql_map.get(row['produk_id'])
        if kode_produk is None:
            print(f"  Produk ID {row['produk_id']} tidak ditemukan di MySQL, skip")
            continue
        produk_info = produk_map.get(kode_produk)
        if produk_info is None:
            print(f"  Produk dengan kode {kode_produk} tidak ditemukan di PostgreSQL, skip")
            continue
        id_produk = produk_info['id_produk']
        valid_from = produk_info['valid_from']

        # 5. Cek apakah id_transaksi sudah ada di fact_persewaan
        cek = pd.read_sql(f"SELECT 1 FROM fact_persewaan WHERE id_transaksi = {row['id_transaksi']}", pg_engine)
        if not cek.empty:
            print(f"  Transaksi {row['id_transaksi']} sudah ada, skip")
            # Update synced = 1 di MySQL
            mysql_engine.execute(f"UPDATE transaksi SET synced = 1 WHERE id_transaksi = {row['id_transaksi']}")
            success += 1
            continue

        # Insert ke fact_persewaan
        insert_fact = f"""
            INSERT INTO fact_persewaan (id_transaksi, id_waktu_fk, id_cabang_fk, id_user_fk, id_produk_fk, valid_from_produk, jumlah_unit, total_harga_sewa, total_denda)
            VALUES ({row['id_transaksi']}, {id_waktu}, {id_cabang}, {id_user}, {id_produk}, '{valid_from}', {row['jumlah']}, {row['total_harga']}, {row['denda']})
        """
        pg_engine.execute(insert_fact)

        # Update synced = 1 di MySQL
        mysql_engine.execute(f"UPDATE transaksi SET synced = 1 WHERE id_transaksi = {row['id_transaksi']}")
        success += 1
        print(f"  Transaksi {row['id_transaksi']} berhasil disinkron.")
    except Exception as e:
        print(f"  Gagal sinkron transaksi {row['id_transaksi']}: {e}")

print(f"Selesai. {success} dari {len(transaksi_df)} transaksi berhasil disinkron.")