<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

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

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id', 'id_cabang');
    }
}