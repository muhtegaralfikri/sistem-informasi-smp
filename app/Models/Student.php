<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'nisn',
        'full_name',
        'gender',
        'birth_date',
        'class_id',
        'guardian_primary_id',
        'address',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function classRoom()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function guardian()
    {
        return $this->belongsTo(Guardian::class, 'guardian_primary_id');
    }

    public function guardians()
    {
        return $this->belongsToMany(Guardian::class)->withPivot('relation')->withTimestamps();
    }
}
