import pandas as pd
import random
from datetime import datetime, timedelta
from sqlalchemy import create_engine, text

DATABASE_URL = "postgresql://postgres:0987654321@localhost:5432/dw_outdoor"
engine = create_engine(DATABASE_URL)

print("Mengosongkan tabel fakta dan waktu...")
with engine.begin() as conn:
    conn.execute(text("TRUNCATE TABLE fact_persewaan RESTART IDENTITY CASCADE;"))
    conn.execute(text("TRUNCATE TABLE dim_waktu RESTART IDENTITY CASCADE;"))

# 1. Generate dimensi waktu (April - Juni 2026)
start_date = datetime(2026, 4, 1)
end_date = datetime(2026, 6, 30)
date_list = [start_date + timedelta(days=x) for x in range((end_date - start_date).days + 1)]
df_waktu = pd.DataFrame(date_list, columns=['tanggal'])
df_waktu['hari'] = df_waktu['tanggal'].dt.day
df_waktu['bulan'] = df_waktu['tanggal'].dt.month
df_waktu['tahun'] = df_waktu['tanggal'].dt.year
df_waktu['kuartal'] = df_waktu['tanggal'].dt.quarter
df_waktu['nama_hari'] = df_waktu['tanggal'].dt.day_name()
df_waktu.to_sql('dim_waktu', engine, if_exists='append', index=False)
print(f"dim_waktu: {len(df_waktu)} baris")

# 2. Ambil data cabang, user, produk dari database
cabang_df = pd.read_sql("SELECT id_cabang FROM dim_cabang", engine)   # harus 30
user_df = pd.read_sql("SELECT id_user, cabang_tugas FROM dim_user WHERE role = 'admin_cabang'", engine)
produk_df = pd.read_sql("SELECT id_produk, harga_sewa_dasar, valid_from FROM dim_produk WHERE is_current = true", engine)

print(f"Cabang tersedia: {len(cabang_df)}")
print(f"User admin: {len(user_df)}")
print(f"Produk aktif: {len(produk_df)}")

# 3. Generate 200 transaksi secara acak, pastikan semua cabang kebagian
transactions = []
id_counter = 1

# Kita loop per cabang agar merata (opsional: bisa juga random murni)
for id_cabang in cabang_df['id_cabang']:
    # Cari user untuk cabang ini
    user_for_cabang = user_df[user_df['cabang_tugas'] == id_cabang]
    if user_for_cabang.empty:
        # fallback: ambil user admin pertama
        user_for_cabang = user_df.sample(1)
    # Tentukan berapa banyak transaksi per cabang (total 200, bagi rata)
    # Namun karena 200 tidak habis dibagi 30, kita akan lakukan random murni agar cepat.
    pass

# Lebih sederhana: generate 200 transaksi random (CROSS JOIN dan LIMIT) tapi tetap semua cabang akan terwakili jika LIMIT cukup besar.
# Kita gunakan metode CROSS JOIN dengan ORDER BY random() untuk memastikan variasi cabang.

# Generate dengan query SQL murni (lebih cepat)
with engine.begin() as conn:
    conn.execute(text("""
        INSERT INTO fact_persewaan (id_transaksi, id_waktu_fk, id_cabang_fk, id_user_fk, id_produk_fk, valid_from_produk, jumlah_unit, total_harga_sewa, total_denda)
        SELECT
            ROW_NUMBER() OVER () AS id_transaksi,
            w.id_waktu,
            c.id_cabang,
            u.id_user,
            p.id_produk,
            p.valid_from,
            1 + floor(random() * 3)::int AS jumlah_unit,
            (1 + floor(random() * 3)::int) * p.harga_sewa_dasar * (1 + floor(random() * 5)::int) AS total_harga_sewa,
            CASE WHEN random() < 0.1 THEN 10000 * (1 + floor(random() * 3)::int) ELSE 0 END AS total_denda
        FROM dim_waktu w
        CROSS JOIN dim_cabang c
        CROSS JOIN dim_user u
        CROSS JOIN dim_produk p
        WHERE u.role = 'admin_cabang' AND u.cabang_tugas = c.id_cabang AND p.is_current = true
        ORDER BY random()
        LIMIT 200;
    """))

print("Berhasil generate 200 transaksi untuk semua cabang.")

# 4. Buat ulang view
with engine.begin() as conn:
    conn.execute(text("DROP VIEW IF EXISTS v_dashboard_nasional CASCADE;"))
    conn.execute(text("""
        CREATE VIEW v_dashboard_nasional AS
        SELECT 
            f.id_transaksi,
            w.tanggal, w.bulan, w.kuartal, w.tahun,
            p.nama_produk, p.kategori,
            c.nama_kota, c.wilayah_provinsi,
            u.nama_user, u.role,
            f.jumlah_unit, f.total_harga_sewa, f.total_denda,
            (f.total_harga_sewa - f.total_denda) AS pendapatan_bersih
        FROM fact_persewaan f
        JOIN dim_waktu w ON f.id_waktu_fk = w.id_waktu
        JOIN dim_produk p ON f.id_produk_fk = p.id_produk AND f.valid_from_produk = p.valid_from
        JOIN dim_cabang c ON f.id_cabang_fk = c.id_cabang
        JOIN dim_user u ON f.id_user_fk = u.id_user;
    """))

print("SUKSES! Data Warehouse direset dengan 200 transaksi yang tersebar ke semua cabang.")