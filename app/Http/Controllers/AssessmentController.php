<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\GradeEntry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssessmentController extends Controller
{
    public function index()
    {
        return response()->json(
            Assessment::with('classSubject')->orderByDesc('created_at')->limit(200)->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'class_subject_id' => ['required', 'exists:class_subjects,id'],
            'semester_id' => ['nullable', 'exists:semesters,id'],
            'type' => ['required', Rule::in(['tugas', 'uh', 'uts', 'uas'])],
            'title' => ['required', 'string', 'max:150'],
            'weight' => ['required', 'integer', 'between:0,100'],
            'max_score' => ['required', 'integer', 'between:1,100'],
            'due_date' => ['nullable', 'date'],
        ]);

        $assessment = Assessment::create($data);

        return response()->json($assessment, 201);
    }

    public function show(Assessment $assessment)
    {
        return response()->json($assessment->load(['classSubject', 'grades']));
    }

    public function update(Request $request, Assessment $assessment)
    {
        $data = $request->validate([
            'class_subject_id' => ['sometimes', 'exists:class_subjects,id'],
            'semester_id' => ['nullable', 'exists:semesters,id'],
            'type' => ['sometimes', Rule::in(['tugas', 'uh', 'uts', 'uas'])],
            'title' => ['sometimes', 'string', 'max:150'],
            'weight' => ['sometimes', 'integer', 'between:0,100'],
            'max_score' => ['sometimes', 'integer', 'between:1,100'],
            'due_date' => ['nullable', 'date'],
        ]);

        $assessment->update($data);

        return response()->json($assessment);
    }

    public function gradesUpsert(Request $request, Assessment $assessment)
    {
        $payload = $request->validate([
            'grades' => ['required', 'array', 'min:1'],
            'grades.*.student_id' => ['required', 'exists:students,id'],
            'grades.*.score' => ['required', 'numeric', 'between:0,100'],
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

        return response()->json($assessment->load('grades'));
    }
}
