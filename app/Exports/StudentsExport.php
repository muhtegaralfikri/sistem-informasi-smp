<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Student::with(['classRoom', 'guardian'])->get();
    }

    public function headings(): array
    {
        return [
            'NIS',
            'NISN',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Kelas',
            'Alamat',
            'Status',
        ];
    }

    public function map($student): array
    {
        return [
            $student->nis,
            $student->nisn,
            $student->full_name,
            $student->gender === 'male' ? 'Laki-laki' : 'Perempuan',
            $student->birth_date ? $student->birth_date->format('d/m/Y') : '',
            $student->classRoom?->name ?? '',
            $student->address ?? '',
            $student->status === 'active' ? 'Aktif' : 'Nonaktif',
        ];
    }
}
