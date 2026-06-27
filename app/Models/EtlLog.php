<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EtlLog extends Model
{
    protected $fillable = [
        'started_at', 'finished_at', 'records_synced', 'status', 'error_message'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];
}