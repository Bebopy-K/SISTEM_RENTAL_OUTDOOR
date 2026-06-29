<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    public function run()
    {
        // Superadmin
        DB::table('users')->updateOrInsert(
            ['username' => 'superadmin'],
            [
                'password' => Hash::make('123456'),
                'role' => 'superadmin',
                'cabang_id' => null,
                'email' => 'superadmin@demo.com',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Manager Palu (cabang_id = 1)
        DB::table('users')->updateOrInsert(
            ['username' => 'manager_palu'],
            [
                'password' => Hash::make('123456'),
                'role' => 'manager',
                'cabang_id' => 1,
                'email' => 'manager.palu@demo.com',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Manager Makassar (cabang_id = 3)
        DB::table('users')->updateOrInsert(
            ['username' => 'manager_makassar'],
            [
                'password' => Hash::make('123456'),
                'role' => 'manager',
                'cabang_id' => 3,
                'email' => 'manager.makassar@demo.com',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Staff Palu (cabang_id = 1)
        DB::table('users')->updateOrInsert(
            ['username' => 'staff_palu'],
            [
                'password' => Hash::make('123456'),
                'role' => 'staff',
                'cabang_id' => 1,
                'email' => 'staff.palu@demo.com',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Staff Makassar (cabang_id = 3)
        DB::table('users')->updateOrInsert(
            ['username' => 'staff_makassar'],
            [
                'password' => Hash::make('123456'),
                'role' => 'staff',
                'cabang_id' => 3,
                'email' => 'staff.makassar@demo.com',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Ubah semua user dengan role 'admin_cabang' menjadi 'manager'
        DB::table('users')
            ->where('role', 'admin_cabang')
            ->update(['role' => 'manager']);

        $this->command->info('Seeder UserRoleSeeder berhasil dijalankan.');
        $this->command->info('User baru:');
        $this->command->info('  - superadmin / 123456');
        $this->command->info('  - manager_palu / 123456');
        $this->command->info('  - manager_makassar / 123456');
        $this->command->info('  - staff_palu / 123456');
        $this->command->info('  - staff_makassar / 123456');
    }
}