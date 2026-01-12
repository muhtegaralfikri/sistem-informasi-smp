<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_subject_id',
        'semester_id',
        'type',
        'title',
        'weight',
        'max_score',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function classSubject()
    {
        return $this->belongsTo(ClassSubject::class);
    }

    public function grades()
    {
        return $this->hasMany(GradeEntry::class);
    }
}
