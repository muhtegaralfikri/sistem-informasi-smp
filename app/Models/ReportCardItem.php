<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCardItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_card_id',
        'subject_id',
        'final_score',
        'predicate',
        'notes',
    ];

    public function reportCard()
    {
        return $this->belongsTo(ReportCard::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
