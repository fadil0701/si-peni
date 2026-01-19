<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'Administrator dengan akses penuh ke semua modul sistem',
            ],
            [
                'name' => 'admin_gudang',
                'display_name' => 'Admin Gudang',
                'description' => 'Admin gudang yang mengelola inventory dan distribusi barang',
            ],
            [
                'name' => 'kepala',
                'display_name' => 'Kepala/Pimpinan',
                'description' => 'Kepala unit kerja yang dapat menyetujui permintaan dan pengajuan',
            ],
            [
                'name' => 'pegawai',
                'display_name' => 'Pegawai/User',
                'description' => 'Pegawai biasa yang dapat mengajukan permintaan dan melihat data',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
