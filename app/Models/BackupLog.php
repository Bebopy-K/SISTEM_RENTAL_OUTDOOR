<?php
// app/Models/BackupLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupLog extends Model
{
    protected $fillable = [
        'name', 'type', 'database', 'filename',
        'size', 'file_path', 'status', 'note', 'completed_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];
}