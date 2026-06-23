<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncToDataWarehouse extends Command
{
    protected $signature = 'sync:dw';
    protected $description = 'Sinkronisasi transaksi dari MySQL ke PostgreSQL Data Warehouse';

    public function handle()
    {
        $this->info('Memulai sinkronisasi...');

        // Ambil transaksi yang belum disinkron (synced = 0)
        $transaksis = DB::connection('mysql')->table('transaksi')->where('synced', 0)->get();

        if ($transaksis->isEmpty()) {
            $this->info('Tidak ada transaksi baru.');
            return 0;
        }

        $this->info("Ditemukan " . $transaksis->count() . " transaksi baru.");

        $success = 0;
        foreach ($transaksis as $t) {
            try {
                // Cari id_waktu berdasarkan tanggal
                $id_waktu = DB::connection('dwh')->table('dim_waktu')->where('tanggal', $t->tanggal)->value('id_waktu');
                if (!$id_waktu) {
                    // Insert ke dim_waktu jika belum ada
                    $id_waktu = DB::connection('dwh')->table('dim_waktu')->insertGetId([
                        'tanggal' => $t->tanggal,
                        'hari' => date('j', strtotime($t->tanggal)),
                        'bulan' => date('n', strtotime($t->tanggal)),
                        'tahun' => date('Y', strtotime($t->tanggal)),
                        'kuartal' => ceil(date('n', strtotime($t->tanggal)) / 3),
                        'nama_hari' => date('l', strtotime($t->tanggal)),
                    ]);
                }

                // Mapping cabang (asumsi id cabang sama)
                $id_cabang = $t->cabang_id;

                // Mapping user (asumsi id user sama)
                $id_user = $t->user_id;

                // Cari produk di Data Warehouse berdasarkan kode_produk dari MySQL
                $produk_mysql = DB::connection('mysql')->table('produk')->where('id_produk', $t->produk_id)->first();
                if (!$produk_mysql) {
                    $this->error("Produk ID {$t->produk_id} tidak ditemukan di MySQL, skip.");
                    continue;
                }
                $produk_dwh = DB::connection('dwh')->table('dim_produk')
                    ->where('kode_produk', $produk_mysql->kode_produk)
                    ->where('is_current', true)
                    ->first();
                if (!$produk_dwh) {
                    $this->error("Produk dengan kode {$produk_mysql->kode_produk} tidak ditemukan di DW, skip.");
                    continue;
                }

                // Insert ke fact_persewaan
                DB::connection('dwh')->table('fact_persewaan')->insert([
                    'id_transaksi' => $t->id_transaksi,
                    'id_waktu_fk' => $id_waktu,
                    'id_cabang_fk' => $id_cabang,
                    'id_user_fk' => $id_user,
                    'id_produk_fk' => $produk_dwh->id_produk,
                    'valid_from_produk' => $produk_dwh->valid_from,
                    'jumlah_unit' => $t->jumlah,
                    'total_harga_sewa' => $t->total_harga,
                    'total_denda' => $t->denda,
                ]);

                // Tandai sudah sinkron
                DB::connection('mysql')->table('transaksi')->where('id_transaksi', $t->id_transaksi)->update(['synced' => 1]);
                $success++;
                $this->info("Transaksi {$t->id_transaksi} berhasil disinkron.");
            } catch (\Exception $e) {
                $this->error("Gagal sinkron transaksi {$t->id_transaksi}: " . $e->getMessage());
            }
        }

        // SESUDAH: Batasi hanya 500 data per batch sinkronisasi
        $transaksis = DB::connection('mysql')->table('transaksi')
        ->where('synced', 0)
        ->limit(500)
        ->get();

        return 0;
    }
}
