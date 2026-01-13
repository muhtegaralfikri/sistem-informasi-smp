<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'subject_id',
        'teacher_id',
    ];

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

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    public function schedules()
    {
        return $this->hasMany(ClassSchedule::class);
    }

    /**
     * Get total weight for this class subject
     */
    public function getTotalWeightAttribute(): int
    {
        return (int) $this->assessments()->sum('weight');
    }

    /**
     * Check if total weight equals 100
     */
    public function isWeightComplete(): bool
    {
        return $this->total_weight === 100;
    }

    /**
     * Calculate final score for a student in this subject
     */
    public function calculateFinalScore(int $studentId): ?float
    {
        $assessments = $this->assessments()->with(['grades' => function ($query) use ($studentId) {
            $query->where('student_id', $studentId);
        }])->get();

        if ($assessments->isEmpty()) {
            return null;
        }

        $totalWeightedScore = 0;
        $totalWeight = 0;

        foreach ($assessments as $assessment) {
            $grade = $assessment->grades->first();
            if ($grade && $grade->score !== null) {
                // Normalize score to 100 scale
                $normalizedScore = ($grade->score / $assessment->max_score) * 100;
                $totalWeightedScore += $normalizedScore * ($assessment->weight / 100);
                $totalWeight += $assessment->weight;
            }
        }

        return $totalWeight > 0 ? round($totalWeightedScore, 2) : null;
    }
}
