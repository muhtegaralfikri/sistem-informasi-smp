<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        User::factory()->create([
            'name' => 'Admin TU',
            'email' => 'admin@smp.test',
            'phone' => '081234567890',
            'role_id' => Role::query()->where('name', 'Admin TU')->first()?->id,
            'password' => Hash::make('admin123'),
        ]);
    }
}
