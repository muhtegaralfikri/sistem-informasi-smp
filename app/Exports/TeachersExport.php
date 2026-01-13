<?php

namespace App\Exports;

use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TeachersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Teacher::all();
    }

    public function headings(): array
    {
        return [
            'NIP',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'No HP',
            'Alamat',
            'Mata Pelajaran',
        ];
    }

    public function map($teacher): array
    {
        return [
            $teacher->nip,
            $teacher->full_name,
            $teacher->gender === 'male' ? 'Laki-laki' : 'Perempuan',
            $teacher->birth_date ? $teacher->birth_date->format('d/m/Y') : '',
            $teacher->phone ?? '',
            $teacher->address ?? '',
            $teacher->subject ?? '',
        ];
    }
}
