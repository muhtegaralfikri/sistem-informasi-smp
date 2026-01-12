<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Jalankan seeder role awal.
     */
    public function run(): void
    {
        $roles = [
            'Admin TU',
            'Guru',
            'Wali Kelas',
            'Kepala Sekolah',
            'Orang Tua',
        ];

        foreach ($roles as $name) {
            Role::query()->updateOrCreate(
                ['name' => $name],
                ['is_system' => true]
            );
        }
    }
}
