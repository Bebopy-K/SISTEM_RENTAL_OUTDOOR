<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\BackupLog;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'backup:run {--type=full : full, incremental, differential}';
    protected $description = 'Backup database MySQL dan PostgreSQL';

    public function handle()
    {
        $type = $this->option('type');
        $this->info('Memulai backup database...');

        $log = BackupLog::create([
            'backup_type' => $type,
            'started_at' => now(),
            'status' => 'processing',
            'message' => 'Memulai backup...',
        ]);

        try {
            // Backup MySQL (OLTP)
            $mysqlPath = $this->backupMySQL($type);
            // Backup PostgreSQL (Data Warehouse)
            $pgsqlPath = $this->backupPostgreSQL($type);

            $log->update([
                'status' => 'success',
                'completed_at' => now(),
                'message' => 'Backup berhasil: MySQL & PostgreSQL',
            ]);

            $this->info('Backup selesai!');
            $this->info('MySQL: ' . $mysqlPath);
            $this->info('PostgreSQL: ' . $pgsqlPath);

            return 0;
        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'completed_at' => now(),
                'message' => 'Error: ' . $e->getMessage(),
            ]);
            $this->error('Backup gagal: ' . $e->getMessage());
            return 1;
        }
    }

    private function backupMySQL($type)
    {
        $dbName = env('DB_DATABASE', 'oltp_rental');
        $username = env('DB_USERNAME', 'root');
        $password = env('DB_PASSWORD', '');
        $host = env('DB_HOST', '127.0.0.1');
        $port = env('DB_PORT', '3306');

        $backupDir = storage_path('backups/mysql');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $date = now()->format('Y-m-d_H-i-s');
        $filename = $dbName . '_' . $type . '_' . $date . '.sql';
        $filepath = $backupDir . '/' . $filename;

        $cmd = "mysqldump -h {$host} -P {$port} -u {$username}";
        if ($password) {
            $cmd .= " -p{$password}";
        }
        $cmd .= " {$dbName} > {$filepath} 2>&1";

        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('MySQL backup failed: ' . implode("\n", $output));
        }

        // Simpan ke backup_logs
        $log = BackupLog::where('status', 'processing')->latest()->first();
        if ($log) {
            $log->update([
                'database_name' => $dbName,
                'file_path' => $filepath,
                'file_size' => filesize($filepath),
            ]);
        }

        return $filepath;
    }

    private function backupPostgreSQL($type)
    {
        $dbName = env('DB_DWH_DATABASE', 'dw_outdoor');
        $username = env('DB_DWH_USERNAME', 'postgres');
        $password = env('DB_DWH_PASSWORD', '0987654321');
        $host = env('DB_DWH_HOST', '127.0.0.1');
        $port = env('DB_DWH_PORT', '5432');

        $backupDir = storage_path('backups/pgsql');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $date = now()->format('Y-m-d_H-i-s');
        $filename = $dbName . '_' . $type . '_' . $date . '.sql';
        $filepath = $backupDir . '/' . $filename;

        $cmd = "pg_dump -h {$host} -p {$port} -U {$username} -d {$dbName} -F p > {$filepath} 2>&1";
        putenv("PGPASSWORD={$password}");
        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('PostgreSQL backup failed: ' . implode("\n", $output));
        }

        return $filepath;
    }
}