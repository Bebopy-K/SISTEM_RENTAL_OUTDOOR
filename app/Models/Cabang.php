<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    protected $table = 'cabang';
    protected $primaryKey = 'id_cabang';
    public $timestamps = true;

    protected $fillable = [
        'kode_cabang',
        'nama_kota',
    ];

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'cabang_id', 'id_cabang');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'cabang_id', 'id_cabang');
    }
}