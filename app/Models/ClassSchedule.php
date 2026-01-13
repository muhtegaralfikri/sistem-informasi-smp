<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_subject_id',
        'day',
        'start_time',
        'end_time',
    ];

    public function classSubject()
    {
        return $this->belongsTo(ClassSubject::class);
    }
}
