<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSheet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    public function sheetsIndex()
    {
        return response()->json(
            AttendanceSheet::with(['classRoom', 'subject', 'teacher'])
                ->orderByDesc('date')
                ->limit(200)
                ->get()
        );
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

        $sheet = AttendanceSheet::create($data);

        return response()->json($sheet, 201);
    }

    public function sheetsShow(AttendanceSheet $sheet)
    {
        return response()->json($sheet->load(['classRoom', 'subject', 'teacher', 'records']));
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
}
