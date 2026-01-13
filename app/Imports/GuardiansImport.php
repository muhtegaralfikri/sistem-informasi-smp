<?php

namespace App\Imports;

use App\Models\Guardian;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class GuardiansImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public function chunkSize(): int
    {
        return 20; // Process 20 rows at a time
    }

    public function collection(Collection $rows)
    {
        $parentRole = Role::where('name', 'Orang Tua')->first();

        foreach ($rows as $row) {
            $phone = $row['no_hp'] ?? $row['phone'] ?? $row['telepon'] ?? null;
            $email = $row['email'] ?? null;

            // Check if guardian exists by phone or email
            $existing = Guardian::where('phone', $phone)
                ->orWhere('email', $email)
                ->first();

            if ($existing) {
                // Update existing guardian
                $existing->update([
                    'full_name' => $row['nama'] ?? $row['full_name'] ?? $row['nama_lengkap'] ?? null,
                    'phone' => $phone,
                    'email' => $email,
                    'relation_default' => $row['hubungan'] ?? $row['relation'] ?? $row['hubungan_default'] ?? null,
                ]);
            } else {
                // Create new guardian with user account
                DB::transaction(function () use ($row, $phone, $email, $parentRole) {
                    $fullName = $row['nama'] ?? $row['full_name'] ?? $row['nama_lengkap'] ?? 'Orang Tua';

                    // Create User account
                    $user = User::create([
                        'name' => $fullName,
                        'email' => $email ?? $phone . '@parent.local', // Use phone-based email if no email
                        'password' => Hash::make('password'), // Default password
                        'role_id' => $parentRole?->id,
                        'status' => 'active',
                    ]);

                    // Create Guardian
                    Guardian::create([
                        'user_id' => $user->id,
                        'full_name' => $fullName,
                        'phone' => $phone,
                        'email' => $email,
                        'relation_default' => $row['hubungan'] ?? $row['relation'] ?? $row['hubungan_default'] ?? null,
                    ]);
                });
            }
        }
    }
}
