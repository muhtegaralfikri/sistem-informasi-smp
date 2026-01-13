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

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Validate that adding this weight won't exceed 100
     */
    public function validateWeightNotExceed(int $newWeight, ?int $excludeId = null): bool
    {
        $query = $this->classSubject->assessments();

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $currentTotal = (int) $query->sum('weight');

        return ($currentTotal + $newWeight) <= 100;
    }

    /**
     * Get remaining weight available
     */
    public function getRemainingWeightAttribute(): int
    {
        return 100 - $this->classSubject->total_weight;
    }

    /**
     * Calculate normalized score for a grade entry
     */
    public function calculateNormalizedScore(?float $rawScore): ?float
    {
        if ($rawScore === null) {
            return null;
        }

        return round(($rawScore / $this->max_score) * 100, 2);
    }
}
