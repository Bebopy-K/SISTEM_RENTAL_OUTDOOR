<?php
// app/Http/Controllers/BackupController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Models\BackupLog;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BackupController extends Controller
{
    // =============================================
    // 1. INDEX (Daftar Backup + Statistik)
    // =============================================
    public function index()
    {
        $backups = BackupLog::orderBy('id', 'desc')->paginate(20);

        // Statistik
        $stats = [
            'total' => BackupLog::count(),
            'full' => BackupLog::where('type', 'full')->count(),
            'incremental' => BackupLog::where('type', 'incremental')->count(),
            'differential' => BackupLog::where('type', 'differential')->count(),
            'success' => BackupLog::where('status', 'success')->count(),
            'failed' => BackupLog::where('status', 'failed')->count(),
        ];

        return view('backup.index', compact('backups', 'stats'));
    }

    // =============================================
    // 2. FULL BACKUP (menggunakan Artisan)
    // =============================================
    public function fullBackup()
    {
        set_time_limit(0); // Tidak ada timeout

        $log = BackupLog::create([
            'name' => 'Full Backup ' . now()->format('Y-m-d H:i:s'),
            'type' => 'full',
            'database' => 'both',
            'status' => 'processing',
        ]);

        try {
            // Jalankan backup via Artisan (jika package spatie/laravel-backup terinstal)
            Artisan::call('backup:run', ['--only-db' => true]);
            $output = Artisan::output();

            // Cari file backup terbaru
            $files = Storage::disk('local')->files('backups');
            $latestFile = collect($files)->sortByDesc(function ($file) {
                return Storage::lastModified($file);
            })->first();

            $log->update([
                'status' => 'success',
                'completed_at' => now(),
                'filename' => basename($latestFile),
                'file_path' => $latestFile,
                'size' => $this->formatSize(Storage::size($latestFile)),
                'note' => $output,
            ]);

            return redirect()->route('backup.index')
                ->with('success', 'Full backup berhasil dibuat.');

        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'note' => $e->getMessage(),
            ]);
            return redirect()->route('backup.index')
                ->with('error', 'Backup gagal: ' . $e->getMessage());
        }
    }

    // =============================================
    // 3. INCREMENTAL BACKUP (Simulasi)
    // =============================================
    public function incrementalBackup()
    {
        set_time_limit(0);

        $lastBackup = BackupLog::where('status', 'success')
            ->whereIn('type', ['full', 'incremental'])
            ->orderBy('completed_at', 'desc')
            ->first();

        $log = BackupLog::create([
            'name' => 'Incremental Backup ' . now()->format('Y-m-d H:i:s'),
            'type' => 'incremental',
            'database' => 'mysql',
            'status' => 'processing',
            'note' => $lastBackup ? 'Since: ' . $lastBackup->completed_at : 'First backup',
        ]);

        try {
            // Simulasi incremental: backup tabel transaksi saja (bisa pakai mysqldump dengan --where)
            // Atau gunakan package yang mendukung incremental
            // Di sini kita gunakan dump dengan kondisi WHERE updated_at > last_backup_time
            $since = $lastBackup ? $lastBackup->completed_at->format('Y-m-d H:i:s') : now()->subDay()->format('Y-m-d H:i:s');
            $filename = 'incremental_' . now()->format('Ymd_His') . '.sql';
            $path = storage_path('app/backups/' . $filename);

            $db = config('database.connections.mysql');
            $command = sprintf(
                'mysqldump -h %s -P %s -u %s %s %s --where="updated_at >= \'%s\' OR created_at >= \'%s\'" %s > %s',
                escapeshellarg($db['host']),
                escapeshellarg($db['port']),
                escapeshellarg($db['username']),
                $db['password'] ? '-p' . escapeshellarg($db['password']) : '',
                escapeshellarg($db['database']),
                $since,
                $since,
                'transaksi',
                escapeshellarg($path)
            );

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception('Incremental backup gagal.');
            }

            $log->update([
                'status' => 'success',
                'completed_at' => now(),
                'filename' => $filename,
                'file_path' => 'backups/' . $filename,
                'size' => $this->formatSize(filesize($path)),
            ]);

            return redirect()->route('backup.index')
                ->with('success', 'Incremental backup berhasil dibuat.');

        } catch (\Exception $e) {
            $log->update(['status' => 'failed', 'note' => $e->getMessage()]);
            return redirect()->route('backup.index')
                ->with('error', 'Backup gagal: ' . $e->getMessage());
        }
    }

    // =============================================
    // 4. DIFFERENTIAL BACKUP
    // =============================================
    public function differentialBackup()
    {
        set_time_limit(0);

        $lastFull = BackupLog::where('status', 'success')
            ->where('type', 'full')
            ->orderBy('completed_at', 'desc')
            ->first();

        if (!$lastFull) {
            return redirect()->route('backup.index')
                ->with('error', 'Tidak ada full backup sebagai basis differential.');
        }

        $log = BackupLog::create([
            'name' => 'Differential Backup ' . now()->format('Y-m-d H:i:s'),
            'type' => 'differential',
            'database' => 'mysql',
            'status' => 'processing',
            'note' => 'Since full backup: ' . $lastFull->completed_at,
        ]);

        try {
            // Mirip incremental, tapi berdasarkan full backup terakhir
            $since = $lastFull->completed_at->format('Y-m-d H:i:s');
            $filename = 'differential_' . now()->format('Ymd_His') . '.sql';
            $path = storage_path('app/backups/' . $filename);

            $db = config('database.connections.mysql');
            $command = sprintf(
                'mysqldump -h %s -P %s -u %s %s %s --where="updated_at >= \'%s\' OR created_at >= \'%s\'" %s > %s',
                escapeshellarg($db['host']),
                escapeshellarg($db['port']),
                escapeshellarg($db['username']),
                $db['password'] ? '-p' . escapeshellarg($db['password']) : '',
                escapeshellarg($db['database']),
                $since,
                $since,
                'transaksi',
                escapeshellarg($path)
            );

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception('Differential backup gagal.');
            }

            $log->update([
                'status' => 'success',
                'completed_at' => now(),
                'filename' => $filename,
                'file_path' => 'backups/' . $filename,
                'size' => $this->formatSize(filesize($path)),
            ]);

            return redirect()->route('backup.index')
                ->with('success', 'Differential backup berhasil dibuat.');

        } catch (\Exception $e) {
            $log->update(['status' => 'failed', 'note' => $e->getMessage()]);
            return redirect()->route('backup.index')
                ->with('error', 'Backup gagal: ' . $e->getMessage());
        }
    }

    // =============================================
    // 5. RESTORE / RECOVERY
    // =============================================
    public function restore($id)
    {
        set_time_limit(0);
        $log = BackupLog::findOrFail($id);

        if ($log->status !== 'success') {
            return redirect()->route('backup.index')
                ->with('error', 'Backup ini tidak valid untuk direstore.');
        }

        try {
            $filePath = storage_path('app/' . $log->file_path);
            if (!file_exists($filePath)) {
                throw new \Exception('File backup tidak ditemukan.');
            }

            // Restore ke MySQL
            $db = config('database.connections.mysql');
            $command = sprintf(
                'mysql -h %s -P %s -u %s %s %s < %s',
                escapeshellarg($db['host']),
                escapeshellarg($db['port']),
                escapeshellarg($db['username']),
                $db['password'] ? '-p' . escapeshellarg($db['password']) : '',
                escapeshellarg($db['database']),
                escapeshellarg($filePath)
            );
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception('Restore gagal.');
            }

            return redirect()->route('backup.index')
                ->with('success', 'Restore berhasil dilakukan dari backup: ' . $log->name);

        } catch (\Exception $e) {
            return redirect()->route('backup.index')
                ->with('error', 'Restore gagal: ' . $e->getMessage());
        }
    }

    // =============================================
    // 6. DOWNLOAD
    // =============================================
    public function download($id)
    {
        $log = BackupLog::findOrFail($id);
        $filePath = storage_path('app/' . $log->file_path);

        if (!file_exists($filePath)) {
            return redirect()->route('backup.index')
                ->with('error', 'File tidak ditemukan.');
        }

        return response()->download($filePath, $log->filename);
    }

    // =============================================
    // 7. DELETE
    // =============================================
    public function destroy($id)
    {
        $log = BackupLog::findOrFail($id);
        if (file_exists(storage_path('app/' . $log->file_path))) {
            unlink(storage_path('app/' . $log->file_path));
        }
        $log->delete();

        return redirect()->route('backup.index')
            ->with('success', 'Backup berhasil dihapus.');
    }

    // =============================================
    // 8. DRP (Disaster Recovery Plan)
    // =============================================
    public function drp()
    {
        return view('backup.drp');
    }

    // =============================================
    // 9. HELPER: Format Size
    // =============================================
    private function formatSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }
}