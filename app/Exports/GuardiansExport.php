<?php

namespace App\Exports;

use App\Models\Guardian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GuardiansExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Guardian::with('students')->get();
    }

    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'No HP',
            'Email',
            'Hubungan',
            'Jumlah Anak',
        ];
    }

    public function map($guardian): array
    {
        return [
            $guardian->full_name,
            $guardian->phone ?? '',
            $guardian->email ?? '',
            $guardian->relation_default ?? '',
            $guardian->students->count() . ' siswa',
        ];
    }
}
