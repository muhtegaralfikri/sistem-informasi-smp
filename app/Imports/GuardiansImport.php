<?php

namespace App\Imports;

use App\Models\Guardian;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuardiansImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Check if guardian exists by phone or email
            $existing = Guardian::where('phone', $row['no_hp'] ?? $row['phone'])
                ->orWhere('email', $row['email'])
                ->first();

            if ($existing) {
                // Update existing guardian
                $existing->update([
                    'full_name' => $row['nama'] ?? $row['full_name'] ?? $row['nama_lengkap'] ?? null,
                    'phone' => $row['no_hp'] ?? $row['phone'] ?? $row['telepon'] ?? null,
                    'email' => $row['email'] ?? null,
                    'relation_default' => $row['hubungan'] ?? $row['relation'] ?? $row['hubungan_default'] ?? null,
                ]);
            } else {
                // Create new guardian
                Guardian::create([
                    'full_name' => $row['nama'] ?? $row['full_name'] ?? $row['nama_lengkap'] ?? null,
                    'phone' => $row['no_hp'] ?? $row['phone'] ?? $row['telepon'] ?? null,
                    'email' => $row['email'] ?? null,
                    'relation_default' => $row['hubungan'] ?? $row['relation'] ?? $row['hubungan_default'] ?? null,
                ]);
            }
        }
    }
}
