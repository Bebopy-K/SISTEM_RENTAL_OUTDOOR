<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // Definisikan konstanta role untuk menghindari typo
    const ROLE_SUPERADMIN = 'superadmin';
    const ROLE_MANAGER = 'manager';
    const ROLE_STAFF = 'staff';

    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $timestamps = true;

    protected $fillable = [
        'username',
        'password',
        'role',
        'cabang_id',
        'google_id',
        'email',
    ];

    protected $hidden = [
        'password',
    ];

    // Relasi ke cabang
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id', 'id_cabang');
    }

    // Relasi ke transaksi (jika ingin staff melihat transaksi miliknya sendiri)
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'user_id', 'id_user');
    }

    // Helper method untuk pengecekan role
    public function isSuperadmin()
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    public function isManager()
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isStaff()
    {
        return $this->role === self::ROLE_STAFF;
    }

    // Cek apakah user memiliki akses ke cabang tertentu (untuk manager/staff)
    public function hasAccessToCabang($cabangId)
    {
        // Superadmin punya akses ke semua cabang
        if ($this->isSuperadmin()) {
            return true;
        }
        // Manager/staff hanya punya akses ke cabangnya sendiri
        return $this->cabang_id == $cabangId;
    }
}