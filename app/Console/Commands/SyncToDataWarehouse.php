<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncToDataWarehouse extends Command
{
    protected $signature = 'sync:dw';
    protected $description = 'Sinkronisasi transaksi dari MySQL ke PostgreSQL Data Warehouse dengan optimasi memori';

    public function handle()
    {
        $this->info('Memulai sinkronisasi...');

        // Gunakan chunk mendongkrak performa & hemat RAM jika data transaksinya masif
        DB::connection('mysql')->table('transaksi')
            ->where('synced', 0)
            ->chunkById(100, function ($transaksis) {

                $this->info("Memproses batch sebanyak " . $transaksis->count() . " transaksi.");

                // 1. Pre-fetch semua data produk dari MySQL untuk batch ini (menghindari query di dalam loop)
                $produkIds = $transaksis->pluck('produk_id')->unique()->toArray();
                $produkMysqlLookups = DB::connection('mysql')->table('produk')
                    ->whereIn('id_produk', $produkIds)
                    ->get()
                    ->keyBy('id_produk');

                // 2. Pre-fetch data produk di DWH berdasarkan kode_produk
                $kodeProduks = $produkMysqlLookups->pluck('kode_produk')->unique()->toArray();
                $produkDwhLookups = DB::connection('dwh')->table('dim_produk')
                    ->whereIn('kode_produk', $kodeProduks)
                    ->where('is_current', true)
                    ->get()
                    ->keyBy('kode_produk');

                foreach ($transaksis as $t) {
                    try {
                        // Bungkus dengan transaksi database DWH per baris agar jika error tidak merusak data lain
                        DB::connection('dwh')->transaction(function () use ($t, $produkMysqlLookups, $produkDwhLookups) {

                            // Ambil id_waktu
                            $id_waktu = DB::connection('dwh')->table('dim_waktu')->where('tanggal', $t->tanggal)->value('id_waktu');
                            if (!$id_waktu) {
                                $id_waktu = DB::connection('dwh')->table('dim_waktu')->insertGetId([
                                    'tanggal' => $t->tanggal,
                                    'hari' => date('j', strtotime($t->tanggal)),
                                    'bulan' => date('n', strtotime($t->tanggal)),
                                    'tahun' => date('Y', strtotime($t->tanggal)),
                                    'kuartal' => ceil(date('n', strtotime($t->tanggal)) / 3),
                                    'nama_hari' => date('l', strtotime($t->tanggal)),
                                ]);
                            }

                            // Lookup produk dari memory cache (bukan dari query database berulang)
                            $pMysql = $produkMysqlLookups->get($t->produk_id);
                            if (!$pMysql) throw new \Exception("Produk ID {$t->produk_id} tidak ditemukan di MySQL.");

                            $pDwh = $produkDwhLookups->get($pMysql->kode_produk);
                            if (!$pDwh) throw new \Exception("Produk kode {$pMysql->kode_produk} tidak ditemukan di DWH.");

                            // Insert ke fact_persewaan
                            DB::connection('dwh')->table('fact_persewaan')->insert([
                                'id_transaksi' => $t->id_transaksi,
                                'id_waktu_fk' => $id_waktu,
                                'id_cabang_fk' => $t->cabang_id,
                                'id_user_fk' => $t->user_id,
                                'id_produk_fk' => $pDwh->id_produk,
                                'valid_from_produk' => $pDwh->valid_from,
                                'jumlah_unit' => $t->jumlah,
                                'total_harga_sewa' => $t->total_harga,
                                'total_denda' => $t->denda,
                            ]);

                            // Tandai sudah sinkron di MySQL
                            DB::connection('mysql')->table('transaksi')->where('id_transaksi', $t->id_transaksi)->update(['synced' => 1]);
                        });

                    } catch (\Exception $e) {
                        $this->error("Gagal sinkron transaksi {$t->id_transaksi}: " . $e->getMessage());
                    }
                }
            });

        $this->info("Proses sinkronisasi selesai.");
        return 0;
    }
}
