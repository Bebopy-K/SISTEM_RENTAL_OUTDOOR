import pandas as pd
from sqlalchemy import create_engine

# Koneksi ke Data Warehouse (dw_outdoor)
dw_engine = create_engine('postgresql://postgres@localhost:5432/dw_outdoor')
# Koneksi ke sistem operasional (oltp_rental)
oltp_engine = create_engine('postgresql://postgres@localhost:5432/oltp_rental')

# 1. Copy cabang
cabang_df = pd.read_sql("SELECT kode_cabang, nama_kota FROM dim_cabang", dw_engine)
cabang_df.to_sql('cabang', oltp_engine, if_exists='append', index=False)
print(f"✅ {len(cabang_df)} cabang berhasil disalin")

# 2. Copy produk (hanya yang aktif)
produk_df = pd.read_sql("SELECT kode_produk, nama_produk, harga_sewa_dasar FROM dim_produk WHERE is_current = true", dw_engine)
produk_df.rename(columns={'harga_sewa_dasar': 'harga_sewa'}, inplace=True)
produk_df.to_sql('produk', oltp_engine, if_exists='append', index=False)
print(f"✅ {len(produk_df)} produk berhasil disalin")

print("Selesai. Sekarang buat user via psql atau lanjutkan script")