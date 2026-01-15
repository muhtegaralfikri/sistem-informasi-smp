<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSheet;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    public function sheetsIndex()
    {
        try {
            $query = AttendanceSheet::with(['classRoom', 'subject', 'teacher'])
                ->orderByDesc('date')
                ->orderByDesc('created_at');

            // Filter by teacher for Guru role
            $teacher = $this->currentTeacher();
            if ($teacher) {
                $query->where('teacher_id', $teacher->id);
            }

            $sheets = $query->limit(200)->get()->map(fn($s) => [
                'id' => $s->id,
                'class' => $s->classRoom->name ?? '-',
                'class_id' => $s->class_id,
                'subject' => $s->subject->name ?? '-',
                'teacher' => $s->teacher->full_name ?? '-',
                'date' => optional($s->date)->format('Y-m-d'),
                'session' => $s->session,
                'locked' => !is_null($s->locked_at),
            ]);

            return response()->json($sheets);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error fetching sheets: ' . $e->getMessage());
            return response()->json(['message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    public function sheetsStore(Request $request)
    {
        $data = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
            'semester_id' => ['nullable', 'exists:semesters,id'],
            'date' => ['required', 'date'],
            'session' => ['nullable', 'string', 'max:30'],
        ]);

        // Auto-set teacher_id for Guru role
        $teacher = $this->currentTeacher();
        if ($teacher && empty($data['teacher_id'])) {
            $data['teacher_id'] = $teacher->id;
        }

        $sheet = AttendanceSheet::create($data);
        $sheet->load(['classRoom', 'subject', 'teacher']);

        return response()->json([
            'id' => $sheet->id,
            'class' => $sheet->classRoom->name ?? '-',
            'class_id' => $sheet->class_id,
            'subject' => $sheet->subject->name ?? '-',
            'teacher' => $sheet->teacher->full_name ?? '-',
            'date' => optional($sheet->date)->format('Y-m-d'),
            'session' => $sheet->session,
            'locked' => !is_null($sheet->locked_at),
        ], 201);
    }

    public function sheetsShow(AttendanceSheet $sheet)
    {
        $sheet->load(['classRoom', 'subject', 'teacher', 'records']);

        return response()->json([
            'id' => $sheet->id,
            'class' => $sheet->classRoom->name ?? '-',
            'class_id' => $sheet->class_id,
            'subject' => $sheet->subject->name ?? '-',
            'teacher' => $sheet->teacher->full_name ?? '-',
            'date' => optional($sheet->date)->format('Y-m-d'),
            'session' => $sheet->session,
            'locked' => !is_null($sheet->locked_at),
            'locked_at' => $sheet->locked_at,
            'records' => $sheet->records,
        ]);
    }

    public function sheetsLock(AttendanceSheet $sheet)
    {
        $sheet->update(['locked_at' => now()]);

        return response()->json($sheet);
    }

    public function recordsUpsert(Request $request, AttendanceSheet $sheet)
    {
        if ($sheet->locked_at) {
            abort(423, 'Sheet sudah dikunci.');
        }

        $payload = $request->validate([
            'records' => ['required', 'array', 'min:1'],
            'records.*.student_id' => ['required', 'exists:students,id'],
            'records.*.status' => ['required', Rule::in(['hadir', 'izin', 'sakit', 'alfa'])],
            'records.*.note' => ['nullable', 'string'],
        ]);

        foreach ($payload['records'] as $rec) {
            AttendanceRecord::updateOrCreate(
                [
                    'attendance_sheet_id' => $sheet->id,
                    'student_id' => $rec['student_id'],
                ],
                [
                    'status' => $rec['status'],
                    'note' => $rec['note'] ?? null,
                ]
            );
        }

        return response()->json($sheet->load('records'));
    }

    public function sheetsDestroy(AttendanceSheet $sheet)
    {
        // Check permissions (e.g. only creator or admin can delete)
        $user = Auth::user();
        $isGuru = $user->role?->name === 'Guru';

        if ($isGuru && $sheet->teacher_id && $sheet->teacher?->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus sheet ini.');
        }

        if ($sheet->locked_at) {
            abort(423, 'Sheet yang sudah dikunci tidak dapat dihapus.');
        }

        // Delete associated records first (if foreign key cascade is not set up, though it usually is)
        $sheet->records()->delete();
        $sheet->delete();

        return response()->noContent();
    }

    private function currentTeacher(): ?Teacher
    {
        $user = Auth::user();
        if (!$user || $user->role?->name !== 'Guru') {
            return null;
        }
        return Teacher::where('user_id', $user->id)->first();
    }
}
