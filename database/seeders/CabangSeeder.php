<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CabangSeeder extends Seeder
{
    public function run()
    {
        $cabang = [
            ['kode_cabang' => 'CBG_PLU', 'nama_kota' => 'Palu'],
            ['kode_cabang' => 'CBG_SIG', 'nama_kota' => 'Sigi'],
            ['kode_cabang' => 'CBG_MKS', 'nama_kota' => 'Makassar'],
            ['kode_cabang' => 'CBG_MDO', 'nama_kota' => 'Manado'],
            ['kode_cabang' => 'CBG_KDI', 'nama_kota' => 'Kendari'],
            ['kode_cabang' => 'CBG_GTO', 'nama_kota' => 'Gorontalo'],
            ['kode_cabang' => 'CBG_BTG', 'nama_kota' => 'Bitung'],
            ['kode_cabang' => 'CBG_TLT', 'nama_kota' => 'Tolitoli'],
            ['kode_cabang' => 'CBG_BUO', 'nama_kota' => 'Buol'],
            ['kode_cabang' => 'CBG_MRW', 'nama_kota' => 'Morowali'],
            ['kode_cabang' => 'CBG_BGI', 'nama_kota' => 'Banggai'],
            ['kode_cabang' => 'CBG_PAR', 'nama_kota' => 'Parigi'],
            ['kode_cabang' => 'CBG_DGL', 'nama_kota' => 'Donggala'],
            ['kode_cabang' => 'CBG_LWK', 'nama_kota' => 'Luwuk'],
            ['kode_cabang' => 'CBG_PSO', 'nama_kota' => 'Poso'],
            ['kode_cabang' => 'CBG_PLM', 'nama_kota' => 'Palembang'],
            ['kode_cabang' => 'CBG_PDG', 'nama_kota' => 'Padang'],
            ['kode_cabang' => 'CBG_BTM', 'nama_kota' => 'Batam'],
            ['kode_cabang' => 'CBG_PKU', 'nama_kota' => 'Pekanbaru'],
            ['kode_cabang' => 'CBG_BKS', 'nama_kota' => 'Bekasi'],
            ['kode_cabang' => 'CBG_SBY', 'nama_kota' => 'Surabaya'],
            ['kode_cabang' => 'CBG_BGR', 'nama_kota' => 'Bogor'],
            ['kode_cabang' => 'CBG_TGR', 'nama_kota' => 'Tangerang'],
            ['kode_cabang' => 'CBG_BDG', 'nama_kota' => 'Bandung'],
            ['kode_cabang' => 'CBG_SMG', 'nama_kota' => 'Semarang'],
            ['kode_cabang' => 'CBG_YOG', 'nama_kota' => 'Yogyakarta'],
            ['kode_cabang' => 'CBG_DPS', 'nama_kota' => 'Denpasar'],
            ['kode_cabang' => 'CBG_LOM', 'nama_kota' => 'Mataram'],
            ['kode_cabang' => 'CBG_KPG', 'nama_kota' => 'Kupang'],
            ['kode_cabang' => 'CBG_BJN', 'nama_kota' => 'Banjarmasin'],
        ];

        DB::table('cabang')->insert($cabang);
    }
}