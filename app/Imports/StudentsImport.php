<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class StudentsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip if NIS already exists
            $existing = Student::where('nis', $row['nis'])->first();
            if ($existing) {
                // Update existing student
                $existing->update([
                    'nisn' => $row['nisn'] ?? null,
                    'full_name' => $row['nama'] ?? $row['full_name'] ?? $row['nama_lengkap'] ?? null,
                    'gender' => $this->parseGender($row['jenis_kelamin'] ?? $row['gender'] ?? $row['jk'] ?? null),
                    'birth_date' => $this->parseDate($row['tanggal_lahir'] ?? $row['birth_date'] ?? $row['tgl_lahir'] ?? null),
                    'address' => $row['alamat'] ?? $row['address'] ?? null,
                    'status' => $row['status'] ?? 'active',
                ]);
            } else {
                // Create new student
                Student::create([
                    'nis' => $row['nis'],
                    'nisn' => $row['nisn'] ?? null,
                    'full_name' => $row['nama'] ?? $row['full_name'] ?? $row['nama_lengkap'] ?? null,
                    'gender' => $this->parseGender($row['jenis_kelamin'] ?? $row['gender'] ?? $row['jk'] ?? null),
                    'birth_date' => $this->parseDate($row['tanggal_lahir'] ?? $row['birth_date'] ?? $row['tgl_lahir'] ?? null),
                    'address' => $row['alamat'] ?? $row['address'] ?? null,
                    'status' => $row['status'] ?? 'active',
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
            // Try different date formats
            if (is_numeric($value)) {
                // Excel serial date
                return \Carbon\Carbon::createFromDate(1900, 1, 1)->addDays($value - 2)->format('Y-m-d');
            }

            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
