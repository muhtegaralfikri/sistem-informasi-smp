<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'subject_id',
        'teacher_id',
        'semester_id',
        'date',
        'session',
        'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'locked_at' => 'datetime',
        ];
    }

    public function records()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
