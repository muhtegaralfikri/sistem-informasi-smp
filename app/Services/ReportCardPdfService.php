<?php

namespace App\Services;

use App\Models\ReportCard;
use App\Models\ReportCardItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportCardPdfService
{
    /**
     * Generate PDF for a report card
     */
    public function generate(ReportCard $reportCard): string
    {
        $reportCard->load(['items.subject', 'student.schoolClass', 'semester.academicYear']);

        // Calculate attendance summary if available
        $attendanceSummary = $this->calculateAttendanceSummary($reportCard);

        // Calculate grade statistics
        $gradeStats = $this->calculateGradeStats($reportCard);

        $data = [
            'reportCard' => $reportCard,
            'attendanceSummary' => $attendanceSummary,
            'gradeStats' => $gradeStats,
            'schoolName' => config('app.name', 'SMP Terpadu'),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('pdf.report-card', $data);
        $pdf->setPaper('A4', 'portrait');

        // Generate filename
        $filename = $this->generateFilename($reportCard);

        // Store PDF
        $path = 'report-cards/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Calculate attendance summary for report card
     */
    private function calculateAttendanceSummary(ReportCard $reportCard): array
    {
        // TODO: Query actual attendance records
        // For now, return placeholder data
        return [
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alfa' => 0,
            'total' => 0,
        ];
    }

    /**
     * Calculate grade statistics
     */
    private function calculateGradeStats(ReportCard $reportCard): array
    {
        $items = $reportCard->items;

        if ($items->isEmpty()) {
            return [
                'average' => 0,
                'highest' => 0,
                'lowest' => 0,
                'total_subjects' => 0,
            ];
        }

        $scores = $items->pluck('final_score')->filter(fn ($score) => $score !== null);

        return [
            'average' => $scores->avg() ?? 0,
            'highest' => $scores->max() ?? 0,
            'lowest' => $scores->min() ?? 0,
            'total_subjects' => $items->count(),
        ];
    }

    /**
     * Generate unique filename for report card
     */
    private function generateFilename(ReportCard $reportCard): string
    {
        $studentName = str_replace(' ', '_', strtolower($reportCard->student->full_name));
        $semester = str_replace(' ', '_', strtolower($reportCard->semester->name));
        $year = $reportCard->semester->academicYear->name;

        return "raport_{$studentName}_{$semester}_{$year}_{$reportCard->id}.pdf";
    }

    /**
     * Stream PDF to browser
     */
    public function stream(ReportCard $reportCard): BinaryFileResponse
    {
        if (!$reportCard->pdf_path) {
            $path = $this->generate($reportCard);
            $reportCard->update(['pdf_path' => $path]);
        }

        $filePath = Storage::disk('public')->path($reportCard->pdf_path);
        return new BinaryFileResponse($filePath);
    }

    /**
     * Delete PDF file
     */
    public function delete(ReportCard $reportCard): bool
    {
        if ($reportCard->pdf_path && Storage::disk('public')->exists($reportCard->pdf_path)) {
            Storage::disk('public')->delete($reportCard->pdf_path);
            $reportCard->update(['pdf_path' => null]);
            return true;
        }

        return false;
    }
}
