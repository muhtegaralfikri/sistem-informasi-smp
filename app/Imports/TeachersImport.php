<?php

namespace App\Imports;

use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

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
                    'gender' => $this->parseGender($row['jenis_kelamin'] ?? $row['gender'] ?? $row['jk'] ?? null),
                    'birth_date' => $this->parseDate($row['tanggal_lahir'] ?? $row['birth_date'] ?? $row['tgl_lahir'] ?? null),
                    'phone' => $row['no_hp'] ?? $row['phone'] ?? $row['telepon'] ?? null,
                    'address' => $row['alamat'] ?? $row['address'] ?? null,
                    'subject' => $row['mapel'] ?? $row['mata_pelajaran'] ?? $row['subject'] ?? null,
                ]);
            } else {
                // Create new teacher
                Teacher::create([
                    'nip' => $row['nip'],
                    'full_name' => $row['nama'] ?? $row['full_name'] ?? $row['nama_lengkap'] ?? null,
                    'gender' => $this->parseGender($row['jenis_kelamin'] ?? $row['gender'] ?? $row['jk'] ?? null),
                    'birth_date' => $this->parseDate($row['tanggal_lahir'] ?? $row['birth_date'] ?? $row['tgl_lahir'] ?? null),
                    'phone' => $row['no_hp'] ?? $row['phone'] ?? $row['telepon'] ?? null,
                    'address' => $row['alamat'] ?? $row['address'] ?? null,
                    'subject' => $row['mapel'] ?? $row['mata_pelajaran'] ?? $row['subject'] ?? null,
                ]);
            }
        }
    }

    private function parseGender($value)
    {
        if (!$value) return 'male';

        $value = strtolower(trim($value));
        if (in_array($value, ['l', 'laki-laki', 'lelaki', 'male', 'pria'])) {
            return 'male';
        }
        if (in_array($value, ['p', 'perempuan', 'female', 'wanita'])) {
            return 'female';
        }
        return 'male';
    }

    private function parseDate($value)
    {
        if (!$value) return null;

        try {
            if (is_numeric($value)) {
                return \Carbon\Carbon::createFromDate(1900, 1, 1)->addDays($value - 2)->format('Y-m-d');
            }
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
