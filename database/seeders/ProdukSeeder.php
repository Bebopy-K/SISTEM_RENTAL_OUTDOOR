<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdukSeeder extends Seeder
{
    public function run()
    {
        $produk = [
            ['kode_produk' => 'TND01', 'nama_produk' => 'Tenda Kap 2-3 Non Flysheet', 'harga_sewa' => 25000],
            ['kode_produk' => 'TND02', 'nama_produk' => 'Tenda Kap 3-4 Non Flysheet', 'harga_sewa' => 35000],
            ['kode_produk' => 'TND03', 'nama_produk' => 'Tenda Kap 4-5 + Flysheet', 'harga_sewa' => 45000],
            ['kode_produk' => 'TND04', 'nama_produk' => 'Tenda Kap 6-7 + Flysheet', 'harga_sewa' => 60000],
            ['kode_produk' => 'TND05', 'nama_produk' => 'Tenda Kap 8-9 + Flysheet', 'harga_sewa' => 90000],
            ['kode_produk' => 'TND06', 'nama_produk' => 'Tenda Kap 10-12 + Flysheet', 'harga_sewa' => 120000],
            ['kode_produk' => 'TND07', 'nama_produk' => 'Tenda Kap 20 + Flysheet + 5 Matras', 'harga_sewa' => 250000],
            ['kode_produk' => 'MAT01', 'nama_produk' => 'Matras', 'harga_sewa' => 5000],
            ['kode_produk' => 'SBT01', 'nama_produk' => 'Sleeping Bag Tipis', 'harga_sewa' => 10000],
            ['kode_produk' => 'SBB01', 'nama_produk' => 'Sleeping Bag Tebal', 'harga_sewa' => 15000],
            ['kode_produk' => 'ABD01', 'nama_produk' => 'Air Bed', 'harga_sewa' => 25000],
            ['kode_produk' => 'FLY01', 'nama_produk' => 'Flysheet', 'harga_sewa' => 10000],
            ['kode_produk' => 'KRL01', 'nama_produk' => 'Keril', 'harga_sewa' => 20000],
            ['kode_produk' => 'HMK01', 'nama_produk' => 'Hamok', 'harga_sewa' => 10000],
            ['kode_produk' => 'GTR01', 'nama_produk' => 'Gitar', 'harga_sewa' => 45000],
            ['kode_produk' => 'CJB01', 'nama_produk' => 'Cajon Box', 'harga_sewa' => 45000],
            ['kode_produk' => 'CJT01', 'nama_produk' => 'Cajon Travel', 'harga_sewa' => 20000],
            ['kode_produk' => 'LPT01', 'nama_produk' => 'Lampu Tenda', 'harga_sewa' => 10000],
            ['kode_produk' => 'HDL01', 'nama_produk' => 'Head Lamp', 'harga_sewa' => 10000],
            ['kode_produk' => 'MKS01', 'nama_produk' => 'Meja + Kursi Set', 'harga_sewa' => 50000],
            ['kode_produk' => 'NST01', 'nama_produk' => 'Nesting', 'harga_sewa' => 15000],
            ['kode_produk' => 'GAS01', 'nama_produk' => 'Gas', 'harga_sewa' => 10000],
            ['kode_produk' => 'KMP01', 'nama_produk' => 'Kompor Kecil', 'harga_sewa' => 15000],
            ['kode_produk' => 'KMP02', 'nama_produk' => 'Kompor Besar', 'harga_sewa' => 30000],
            ['kode_produk' => 'BBQ01', 'nama_produk' => 'Barbeque Grill Set', 'harga_sewa' => 30000],
            ['kode_produk' => 'CAM01', 'nama_produk' => 'Canon 1100D', 'harga_sewa' => 80000],
            ['kode_produk' => 'CAM02', 'nama_produk' => 'Canon 660D', 'harga_sewa' => 100000],
            ['kode_produk' => 'CAM03', 'nama_produk' => 'Canon 700D', 'harga_sewa' => 120000],
            ['kode_produk' => 'CAM04', 'nama_produk' => 'Canon 60D', 'harga_sewa' => 130000],
            ['kode_produk' => 'CAM05', 'nama_produk' => 'Nikon D3000', 'harga_sewa' => 65000],
            ['kode_produk' => 'CAM06', 'nama_produk' => 'Xiaomi Yi Set', 'harga_sewa' => 50000],
            ['kode_produk' => 'CAM07', 'nama_produk' => 'Fujifilm X A10', 'harga_sewa' => 150000],
            ['kode_produk' => 'TRP01', 'nama_produk' => 'Tripod Stand', 'harga_sewa' => 20000],
            ['kode_produk' => 'TRP02', 'nama_produk' => 'Tripod Gorilla', 'harga_sewa' => 10000],
            ['kode_produk' => 'SPK01', 'nama_produk' => 'Speaker Travel', 'harga_sewa' => 10000],
            ['kode_produk' => 'PKT01', 'nama_produk' => 'Paket 1 (2-3 Tenda + Kompor Kecil + Nesting + Matras)', 'harga_sewa' => 55000],
            ['kode_produk' => 'PKT02', 'nama_produk' => 'Paket 2 (Tenda 4-5 + Flysheet + Kompor Besar + Nesting + 2 Matras)', 'harga_sewa' => 125000],
            ['kode_produk' => 'PKT03', 'nama_produk' => 'Paket Komplit (Tenda 6-7 + Flysheet + Kompor Besar + Nesting + Gas + 4 Matras + 4 Sleeping Bag Tebal + Lampu + Headlamp)', 'harga_sewa' => 250000],
        ];

        DB::table('produk')->insert($produk);
    }
}