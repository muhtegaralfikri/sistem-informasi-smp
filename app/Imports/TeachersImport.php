<?php

namespace App\Imports;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;

class TeachersImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Check if teacher exists by NIP or full_name
            $existing = Teacher::where('nip', $row['nip'])->first();

            if ($existing) {
                // Update existing teacher
                $existing->update([
                    'full_name' => $row['nama'] ?? $row['full_name'] ?? $row['nama_lengkap'] ?? null,
                    'phone' => $row['no_hp'] ?? $row['phone'] ?? $row['telepon'] ?? null,
                    'email' => $row['email'] ?? null,
                    'status' => $this->parseStatus($row['status'] ?? null),
                ]);
            } else {
                // Create new teacher with user
                $fullName = $row['nama'] ?? $row['full_name'] ?? $row['nama_lengkap'] ?? null;
                $nip = $row['nip'];

                // Create user account for teacher
                $user = User::create([
                    'name' => $fullName,
                    'email' => $row['email'] ?? strtolower(str_replace(' ', '.', $fullName)) . '@sis.dev',
                    'password' => Hash::make('password123'), // Default password
                ]);

                // Assign teacher role
                $user->roles()->attach(\App\Models\Role::where('name', 'Guru')->first()->id);

                // Create teacher record
                Teacher::create([
                    'user_id' => $user->id,
                    'nip' => $nip,
                    'full_name' => $fullName,
                    'phone' => $row['no_hp'] ?? $row['phone'] ?? $row['telepon'] ?? null,
                    'email' => $row['email'] ?? null,
                    'status' => $this->parseStatus($row['status'] ?? null),
                ]);
            }
        }
    }

    private function parseStatus($value)
    {
        if (!$value) return 'active';

        $value = strtolower(trim($value));
        if (in_array($value, ['aktif', 'active', 'a', '1', 'yes', 'ya'])) {
            return 'active';
        }
        if (in_array($value, ['nonaktif', 'non-aktif', 'inactive', 'i', '0', 'no', 'tidak'])) {
            return 'inactive';
        }
        return 'active';
    }
}
