<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSheet;
use App\Models\Announcement;
use App\Models\Guardian;
use App\Models\ReportCard;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ParentPortalController extends Controller
{
    private function getGuardian(): ?Guardian
    {
        return Auth::user()?->guardian;
    }

    /**
     * Get dashboard for logged in parent
     */
    public function dashboard(Request $request)
    {
        $guardian = $this->getGuardian();

        if (!$guardian) {
            return response()->json(['message' => 'Data wali tidak ditemukan'], 404);
        }

        $students = $guardian->students()->with(['schoolClass', 'academicYear'])->get();

        // Get announcements for parents
        $announcements = Announcement::where('published_at', '!=', null)
            ->where(function ($query) {
                $query->where('target_scope', 'all')
                    ->orWhere('target_scope', 'parents');
            })
            ->orderByDesc('published_at')
            ->limit(10)
            ->get();

        // Get attendance summary for each student
        $studentsWithStats = $students->map(function ($student) {
            $attendanceSummary = $this->getAttendanceSummary($student->id);
            $reportCards = $this->getLatestReportCards($student->id);

            return [
                'id' => $student->id,
                'full_name' => $student->full_name,
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'class' => $student->schoolClass?->name,
                'academic_year' => $student->academicYear?->name,
                'photo' => $student->photo,
                'attendance_summary' => $attendanceSummary,
                'latest_report_cards' => $reportCards,
            ];
        });

        return response()->json([
            'guardian' => [
                'full_name' => $guardian->full_name,
                'phone' => $guardian->phone,
                'email' => $guardian->email,
            ],
            'students' => $studentsWithStats,
            'announcements' => $announcements,
        ]);
    }

    /**
     * Get attendance records for a specific student
     */
    public function studentAttendance(Request $request, int $studentId)
    {
        $guardian = $this->getGuardian();

        if (!$guardian) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Verify the student belongs to this guardian
        $student = $guardian->students()->where('students.id', $studentId)->first();

        if (!$student) {
            return response()->json(['message' => 'Siswa tidak ditemukan atau bukan anak Anda'], 404);
        }

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $limit = $request->query('limit', 50);

        $query = AttendanceRecord::whereHas('attendanceSheet', function ($q) use ($studentId) {
            $q->whereHas('classRoom', function ($classQuery) use ($studentId) {
                $classQuery->whereHas('students', function ($sQuery) use ($studentId) {
                    $sQuery->where('students.id', $studentId);
                });
            });
        })->with('attendanceSheet');

        if ($startDate) {
            $query->whereHas('attendanceSheet', function ($q) use ($startDate) {
                $q->where('date', '>=', $startDate);
            });
        }

        if ($endDate) {
            $query->whereHas('attendanceSheet', function ($q) use ($endDate) {
                $q->where('date', '<=', $endDate);
            });
        }

        $records = $query->orderByDesc('created_at')->limit($limit)->get();

        // Group by date for better display
        $groupedRecords = $records->groupBy(fn ($record) => $record->attendanceSheet->date->format('Y-m-d'));

        return response()->json([
            'student' => [
                'id' => $student->id,
                'full_name' => $student->full_name,
                'nis' => $student->nis,
            ],
            'attendance_summary' => $this->getAttendanceSummary($studentId, $startDate, $endDate),
            'records' => $groupedRecords,
        ]);
    }

    /**
     * Get report cards for a specific student
     */
    public function studentReportCards(Request $request, int $studentId)
    {
        $guardian = $this->getGuardian();

        if (!$guardian) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Verify the student belongs to this guardian
        $student = $guardian->students()->where('students.id', $studentId)->first();

        if (!$student) {
            return response()->json(['message' => 'Siswa tidak ditemukan atau bukan anak Anda'], 404);
        }

        $reportCards = ReportCard::with(['items.subject', 'semester.academicYear'])
            ->where('student_id', $studentId)
            ->where('status', 'published')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'student' => [
                'id' => $student->id,
                'full_name' => $student->full_name,
                'nis' => $student->nis,
                'class' => $student->schoolClass?->name,
            ],
            'report_cards' => $reportCards->map(function ($card) {
                return [
                    'id' => $card->id,
                    'semester' => $card->semester->name,
                    'academic_year' => $card->semester->academicYear->name,
                    'status' => $card->status,
                    'average_score' => $card->average_score,
                    'total_score' => $card->total_score,
                    'remarks' => $card->remarks,
                    'published_at' => $card->published_at,
                    'pdf_available' => !empty($card->pdf_path),
                    'pdf_url' => $card->pdf_path ? Storage::url($card->pdf_path) : null,
                    'items' => $card->items->map(function ($item) {
                        return [
                            'subject' => $item->subject->name,
                            'final_score' => $item->final_score,
                            'predicate' => $item->predicate,
                            'notes' => $item->notes,
                        ];
                    }),
                ];
            }),
        ]);
    }

    /**
     * Download report card PDF
     */
    public function downloadReportCard(Request $request, int $studentId, int $reportCardId): BinaryFileResponse
    {
        $guardian = $this->getGuardian();

        if (!$guardian) {
            abort(403, 'Unauthorized');
        }

        // Verify the student belongs to this guardian
        $student = $guardian->students()->where('students.id', $studentId)->first();

        if (!$student) {
            abort(404, 'Siswa tidak ditemukan atau bukan anak Anda');
        }

        $reportCard = ReportCard::where('id', $reportCardId)
            ->where('student_id', $studentId)
            ->where('status', 'published')
            ->firstOrFail();

        if (!$reportCard->pdf_path) {
            abort(404, 'PDF belum tersedia');
        }

        $filePath = Storage::disk('public')->path($reportCard->pdf_path);
        return new BinaryFileResponse($filePath);
    }

    /**
     * Get attendance summary for a student
     */
    private function getAttendanceSummary(int $studentId, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = AttendanceRecord::where('student_id', $studentId);

        if ($startDate) {
            $query->whereHas('attendanceSheet', function ($q) use ($startDate) {
                $q->where('date', '>=', $startDate);
            });
        }

        if ($endDate) {
            $query->whereHas('attendanceSheet', function ($q) use ($endDate) {
                $q->where('date', '<=', $endDate);
            });
        }

        $records = $query->get();

        return [
            'hadir' => $records->where('status', 'hadir')->count(),
            'izin' => $records->where('status', 'izin')->count(),
            'sakit' => $records->where('status', 'sakit')->count(),
            'alfa' => $records->where('status', 'alfa')->count(),
            'total' => $records->count(),
        ];
    }

    /**
     * Get latest report cards for a student
     */
    private function getLatestReportCards(int $studentId): array
    {
        $reportCards = ReportCard::with('semester')
            ->where('student_id', $studentId)
            ->where('status', 'published')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        return $reportCards->map(function ($card) {
            return [
                'id' => $card->id,
                'semester' => $card->semester->name,
                'average_score' => $card->average_score,
                'pdf_available' => !empty($card->pdf_path),
            ];
        })->toArray();
    }

    /**
     * Get announcements for parents
     */
    public function announcements(Request $request)
    {
        $query = Announcement::where('published_at', '!=', null)
            ->where(function ($q) {
                $q->where('target_scope', 'all')
                    ->orWhere('target_scope', 'parents');
            })
            ->orderByDesc('published_at');

        if ($request->query('limit')) {
            $query->limit($request->query('limit'));
        }

        $announcements = $query->get();

        return response()->json($announcements);
    }
}
