<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwoFactorCode extends Model
{
    protected $table = 'two_factor_codes';
    
    protected $fillable = [
        'user_id', 'code', 'expires_at', 'used'
    ];
    
    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}