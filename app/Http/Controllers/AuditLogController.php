<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        // Hanya superadmin yang bisa mengakses
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Hanya superadmin yang dapat mengakses halaman ini.');
        }

        $query = AuditLog::orderBy('created_at', 'desc');

        // Filter berdasarkan action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter berdasarkan user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->paginate(20);
        $actions = AuditLog::distinct()->pluck('action');
        $users = \App\Models\User::select('id_user', 'username')->get();

        return view('audit.index', compact('logs', 'actions', 'users'));
    }
}