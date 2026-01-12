<?php

namespace App\Http\Controllers;

use App\Models\ReportCard;
use App\Models\ReportCardItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportCardController extends Controller
{
    public function index()
    {
        return response()->json(
            ReportCard::with(['student', 'semester'])->orderByDesc('created_at')->limit(200)->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
            'status' => ['nullable', Rule::in(['draft', 'approved', 'published'])],
            'remarks' => ['nullable', 'string'],
        ]);

        $report = ReportCard::updateOrCreate(
            [
                'student_id' => $data['student_id'],
                'semester_id' => $data['semester_id'],
            ],
            [
                'status' => $data['status'] ?? 'draft',
                'remarks' => $data['remarks'] ?? null,
            ]
        );

        return response()->json($report, 201);
    }

    public function show(ReportCard $reportCard)
    {
        return response()->json($reportCard->load(['items', 'student', 'semester']));
    }

    public function update(Request $request, ReportCard $reportCard)
    {
        $data = $request->validate([
            'status' => ['sometimes', Rule::in(['draft', 'approved', 'published'])],
            'remarks' => ['nullable', 'string'],
            'approved_by' => ['nullable', 'exists:users,id'],
            'pdf_path' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'total_score' => ['nullable', 'numeric'],
            'average_score' => ['nullable', 'numeric'],
        ]);

        $reportCard->update($data);

        return response()->json($reportCard);
    }

    public function itemsUpsert(Request $request, ReportCard $reportCard)
    {
        $payload = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.subject_id' => ['required', 'exists:subjects,id'],
            'items.*.final_score' => ['required', 'numeric', 'between:0,100'],
            'items.*.predicate' => ['nullable', 'string', 'max:5'],
            'items.*.notes' => ['nullable', 'string'],
        ]);

        foreach ($payload['items'] as $item) {
            ReportCardItem::updateOrCreate(
                [
                    'report_card_id' => $reportCard->id,
                    'subject_id' => $item['subject_id'],
                ],
                [
                    'final_score' => $item['final_score'],
                    'predicate' => $item['predicate'] ?? null,
                    'notes' => $item['notes'] ?? null,
                ]
            );
        }

        return response()->json($reportCard->load('items'));
    }

    public function publish(ReportCard $reportCard)
    {
        $reportCard->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return response()->json($reportCard);
    }
}
