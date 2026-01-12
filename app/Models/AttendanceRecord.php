<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_sheet_id',
        'student_id',
        'status',
        'note',
    ];

    public function sheet()
    {
        return $this->belongsTo(AttendanceSheet::class, 'attendance_sheet_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
