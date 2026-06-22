<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    // 1. Deklarasikan nama tabel sesuai di database MySQL Anda
    protected $table = 'transaksi';

    // 2. Deklarasikan Primary Key asli dari tabel tersebut
    protected $primaryKey = 'id_transaksi';

    // 3. Daftarkan kolom yang diizinkan untuk diisi massal (Mass Assignment)
    protected $fillable = [
        'tanggal',
        'produk_id',
        'jumlah',
        'durasi',
        'cabang_id',
        'user_id',
        'total_harga',
        'denda'
    ];

    // 4. Hubungan Relasi Ke Tabel Produk
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'id_produk');
    }

    // 5. Hubungan Relasi Ke Tabel Cabang
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id', 'id_cabang');
    }

    // 6. Hubungan Relasi Ke Tabel Users (Staf Kasir)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}