<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\GradeEntry;
use App\Models\ClassSubject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssessmentController extends Controller
{
    public function index()
    {
        return response()->json(
            Assessment::with('classSubject.subject', 'classSubject.classRoom', 'semester')
                ->orderByDesc('created_at')
                ->limit(200)
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'class_subject_id' => ['required', 'exists:class_subjects,id'],
            'semester_id' => ['nullable', 'exists:semesters,id'],
            'type' => ['required', Rule::in(['tugas', 'uh', 'uts', 'uas'])],
            'title' => ['required', 'string', 'max:150'],
            'weight' => ['required', 'integer', 'between:1,100'],
            'max_score' => ['required', 'integer', 'between:1,100'],
            'due_date' => ['nullable', 'date'],
        ]);

        $classSubject = ClassSubject::findOrFail($data['class_subject_id']);

        // Validate weight won't exceed 100
        $currentTotal = (int) $classSubject->assessments()->sum('weight');
        if ($currentTotal + $data['weight'] > 100) {
            return response()->json([
                'message' => 'Total bobot tidak boleh melebihi 100%',
                'current_total' => $currentTotal,
                'requested_weight' => $data['weight'],
                'remaining' => 100 - $currentTotal,
            ], 422);
        }

        $assessment = Assessment::create($data);

        return response()->json($assessment->load('classSubject'), 201);
    }

    public function show(Assessment $assessment)
    {
        return response()->json($assessment->load(['classSubject.subject', 'classSubject.classRoom', 'grades.student']));
    }

    public function update(Request $request, Assessment $assessment)
    {
        $data = $request->validate([
            'class_subject_id' => ['sometimes', 'exists:class_subjects,id'],
            'semester_id' => ['nullable', 'exists:semesters,id'],
            'type' => ['sometimes', Rule::in(['tugas', 'uh', 'uts', 'uas'])],
            'title' => ['sometimes', 'string', 'max:150'],
            'weight' => ['sometimes', 'integer', 'between:1,100'],
            'max_score' => ['sometimes', 'integer', 'between:1,100'],
            'due_date' => ['nullable', 'date'],
        ]);

        $classSubjectId = $data['class_subject_id'] ?? $assessment->class_subject_id;
        $classSubject = ClassSubject::findOrFail($classSubjectId);

        // Validate weight won't exceed 100 (excluding current assessment)
        if (isset($data['weight'])) {
            $currentTotal = (int) $classSubject->assessments()
                ->where('id', '!=', $assessment->id)
                ->sum('weight');

            if ($currentTotal + $data['weight'] > 100) {
                return response()->json([
                    'message' => 'Total bobot tidak boleh melebihi 100%',
                    'current_total' => $currentTotal,
                    'requested_weight' => $data['weight'],
                    'remaining' => 100 - $currentTotal,
                ], 422);
            }
        }

        $assessment->update($data);

        return response()->json($assessment->load('classSubject'));
    }

    public function gradesUpsert(Request $request, Assessment $assessment)
    {
        $payload = $request->validate([
            'grades' => ['required', 'array', 'min:1'],
            'grades.*.student_id' => ['required', 'exists:students,id'],
            'grades.*.score' => ['required', 'numeric', 'between:0,' . $assessment->max_score],
        ]);

        foreach ($payload['grades'] as $item) {
            GradeEntry::updateOrCreate(
                [
                    'assessment_id' => $assessment->id,
                    'student_id' => $item['student_id'],
                ],
                [
                    'score' => $item['score'],
                ]
            );
        }

        return response()->json($assessment->load('grades.student'));
    }

    /**
     * Get assessments by class subject with weight summary
     */
    public function byClassSubject(Request $request, int $classSubjectId)
    {
        $classSubject = ClassSubject::with([
            'assessments.grades.student',
            'subject',
            'classRoom'
        ])->findOrFail($classSubjectId);

        $assessments = $classSubject->assessments;
        $totalWeight = $assessments->sum('weight');
        $isComplete = $totalWeight === 100;

        return response()->json([
            'class_subject' => $classSubject,
            'assessments' => $assessments,
            'weight_summary' => [
                'total_weight' => $totalWeight,
                'is_complete' => $isComplete,
                'remaining' => 100 - $totalWeight,
            ],
        ]);
    }

    /**
     * Calculate final scores for all students in a class subject
     */
    public function calculateFinalScores(Request $request, int $classSubjectId)
    {
        $classSubject = ClassSubject::with([
            'classRoom.students',
            'subject',
        ])->findOrFail($classSubjectId);

        // Check if weight is complete
        if (!$classSubject->isWeightComplete()) {
            return response()->json([
                'message' => 'Total bobot belum mencapai 100%',
                'current_total' => $classSubject->total_weight,
                'remaining' => 100 - $classSubject->total_weight,
            ], 422);
        }

        $students = $classSubject->classRoom->students;
        $finalScores = [];

        foreach ($students as $student) {
            $finalScore = $classSubject->calculateFinalScore($student->id);
            $finalScores[] = [
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'nis' => $student->nis,
                'final_score' => $finalScore,
                'predicate' => $finalScore !== null ? $this->calculatePredicate($finalScore) : null,
            ];
        }

        return response()->json([
            'class_subject' => [
                'id' => $classSubject->id,
                'class' => $classSubject->classRoom->name,
                'subject' => $classSubject->subject->name,
            ],
            'final_scores' => $finalScores,
        ]);
    }

    /**
     * Calculate predicate based on score
     */
    private function calculatePredicate(float $score): string
    {
        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B';
        if ($score >= 70) return 'C';
        if ($score >= 60) return 'D';
        return 'E';
    }
}
