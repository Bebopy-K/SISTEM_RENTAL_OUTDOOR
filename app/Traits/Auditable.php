<?php
// app/Traits/Auditable.php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::logActivity('create', $model);
        });

        static::updated(function ($model) {
            self::logActivity('update', $model);
        });

        static::deleted(function ($model) {
            self::logActivity('delete', $model);
        });
    }

    protected static function logActivity($action, $model)
    {
        if (Auth::check()) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'username' => Auth::user()->username,
                'role' => Auth::user()->role,
                'action' => $action . '_' . class_basename($model),
                'description' => class_basename($model) . ' ' . $action . ' ID: ' . $model->getKey(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}