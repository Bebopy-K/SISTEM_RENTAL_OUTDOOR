<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SyncReverse extends Command
{
    protected $signature = 'sync:reverse';
    protected $description = 'Salin data dari Data Warehouse (PostgreSQL) ke sistem operasional (MySQL)';

    public function handle()
    {
        $this->info('Memulai reverse sinkronisasi dari DW ke OLTP...');

        $dwh = DB::connection('dwh');
        $mysql = DB::connection('mysql');

        // Ambil mapping dari MySQL berdasarkan kode unik
        $cabangMap = $mysql->table('cabang')->pluck('id_cabang', 'kode_cabang')->toArray();
        $userMap = $mysql->table('users')->pluck('id_user', 'username')->toArray();
        $produkMap = $mysql->table('produk')->pluck('id_produk', 'kode_produk')->toArray();

        // Ambil semua transaksi dari DW dengan join ke dimensi
        $transaksiDW = $dwh->table('fact_persewaan as f')
            ->join('dim_waktu as w', 'f.id_waktu_fk', '=', 'w.id_waktu')
            ->join('dim_cabang as dc', 'f.id_cabang_fk', '=', 'dc.id_cabang')
            ->join('dim_user as du', 'f.id_user_fk', '=', 'du.id_user')
            ->join('dim_produk as dp', function ($join) {
                $join->on('f.id_produk_fk', '=', 'dp.id_produk')
                     ->on('f.valid_from_produk', '=', 'dp.valid_from');
            })
            ->select(
                'f.id_transaksi',
                'w.tanggal',
                'dc.kode_cabang',
                'du.kode_user',
                'dp.kode_produk',
                'f.jumlah_unit as jumlah',
                'f.total_harga_sewa as total_harga',
                'f.total_denda as denda'
            )
            ->get();

        $this->info("Ditemukan " . $transaksiDW->count() . " transaksi di Data Warehouse.");

        $inserted = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($transaksiDW as $row) {
            // Cek apakah id_transaksi sudah ada di MySQL
            $exists = $mysql->table('transaksi')->where('id_transaksi', $row->id_transaksi)->exists();
            if ($exists) {
                $skipped++;
                continue;
            }

            // Mapping foreign key berdasarkan kode
            $cabangId = $cabangMap[$row->kode_cabang] ?? null;
            $userId = $userMap[$row->kode_user] ?? null;
            $produkId = $produkMap[$row->kode_produk] ?? null;

            if (!$cabangId || !$userId || !$produkId) {
                $this->error("Gagal mapping untuk transaksi {$row->id_transaksi} (cabang={$row->kode_cabang}, user={$row->kode_user}, produk={$row->kode_produk})");
                $errors++;
                continue;
            }

            // Insert ke MySQL
            try {
                $mysql->table('transaksi')->insert([
                    'id_transaksi' => $row->id_transaksi,
                    'tanggal' => $row->tanggal,
                    'produk_id' => $produkId,
                    'jumlah' => $row->jumlah,
                    'durasi' => 1, // default
                    'cabang_id' => $cabangId,
                    'user_id' => $userId,
                    'total_harga' => $row->total_harga,
                    'denda' => $row->denda,
                    'synced' => 1, // sudah tersinkron
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                $inserted++;
                if ($inserted % 20 == 0) {
                    $this->info("Progres: {$inserted} transaksi dimasukkan.");
                }
            } catch (\Exception $e) {
                $this->error("Gagal insert transaksi {$row->id_transaksi}: " . $e->getMessage());
                $errors++;
            }
        }

        $this->info("Selesai. {$inserted} transaksi ditambahkan ke MySQL. {$skipped} sudah ada (skip). {$errors} error.");
    }
}