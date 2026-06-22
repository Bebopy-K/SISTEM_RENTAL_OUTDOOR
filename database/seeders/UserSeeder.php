<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Superadmin
        DB::table('users')->insert([
            'username' => 'superadmin',
            'password' => Hash::make('123456'),
            'role' => 'superadmin',
            'cabang_id' => null,
        ]);

        // Ambil semua cabang
        $cabangs = DB::table('cabang')->get();

        foreach ($cabangs as $cabang) {
            $username = 'adm_' . strtolower(str_replace(' ', '', $cabang->nama_kota));
            DB::table('users')->insert([
                'username' => $username,
                'password' => Hash::make('123456'),
                'role' => 'admin_cabang',
                'cabang_id' => $cabang->id_cabang,
            ]);
        }
    }
}