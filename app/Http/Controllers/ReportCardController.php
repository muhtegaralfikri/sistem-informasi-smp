<?php

namespace App\Http\Controllers;

use App\Models\ReportCard;
use App\Models\ReportCardItem;
use App\Services\ReportCardPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ReportCardController extends Controller
{
    private ReportCardPdfService $pdfService;

    public function __construct(ReportCardPdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

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
        return response()->json($reportCard->load(['items.subject', 'student.schoolClass', 'semester.academicYear']));
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

        // Update totals
        $scores = collect($payload['items'])->pluck('final_score');
        $reportCard->update([
            'total_score' => $scores->sum(),
            'average_score' => $scores->avg(),
        ]);

        return response()->json($reportCard->load('items.subject'));
    }

    public function publish(ReportCard $reportCard)
    {
        $reportCard->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return response()->json($reportCard);
    }

    /**
     * Generate PDF for report card
     */
    public function generatePdf(ReportCard $reportCard)
    {
        $path = $this->pdfService->generate($reportCard);
        $reportCard->update(['pdf_path' => $path]);

        return response()->json([
            'message' => 'PDF berhasil dibuat',
            'pdf_path' => $path,
            'url' => Storage::url($path),
        ]);
    }

    /**
     * Download PDF report card
     */
    public function downloadPdf(ReportCard $reportCard)
    {
        if (!$reportCard->pdf_path) {
            return response()->json(['message' => 'PDF belum dibuat'], 404);
        }

        return $this->pdfService->stream($reportCard);
    }

    /**
     * Approve report card (for Wali Kelas)
     */
    public function approve(Request $request, ReportCard $reportCard)
    {
        if ($reportCard->status === 'published') {
            return response()->json(['message' => 'Raport sudah dipublish, tidak dapat diubah'], 422);
        }

        $reportCard->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return response()->json($reportCard);
    }

    /**
     * Get report cards by student
     */
    public function byStudent(Request $request, int $studentId)
    {
        $reportCards = ReportCard::with(['items.subject', 'semester'])
            ->where('student_id', $studentId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($reportCards);
    }
}
