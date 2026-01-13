<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['classRoom', 'guardian']);

        // Filter by class_id
        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhereHas('classRoom', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Handle JSON API requests
        if ($request->wantsJson()) {
            return response()->json(
                $query->orderBy('full_name')->limit(200)->get()
            );
        }

        // Handle AJAX requests for partial table refresh
        if ($request->ajax()) {
            return view('admin.students.partials.table', [
                'students' => $query->orderBy('full_name')->paginate(15)->withQueryString(),
            ]);
        }

        return view('admin.students.index', [
            'students' => $query->orderBy('full_name')->paginate(15)->withQueryString(),
            'classes' => \App\Models\SchoolClass::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nis' => ['required', 'string', 'max:30', 'unique:students,nis'],
            'nisn' => ['required', 'string', 'max:30', 'unique:students,nisn'],
            'full_name' => ['required', 'string', 'max:150'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'birth_date' => ['nullable', 'date'],
            'class_id' => ['nullable', 'exists:classes,id'],
            'guardian_primary_id' => ['nullable', 'exists:guardians,id'],
            'address' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $student = Student::create($data);

        return response()->json($student->load(['classRoom', 'guardian']), 201);
    }

    public function show(Student $student)
    {
        return response()->json($student->load(['classRoom', 'guardian']));
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'nis' => ['sometimes', 'string', 'max:30', Rule::unique('students', 'nis')->ignore($student->id)],
            'nisn' => ['sometimes', 'string', 'max:30', Rule::unique('students', 'nisn')->ignore($student->id)],
            'full_name' => ['sometimes', 'string', 'max:150'],
            'gender' => ['sometimes', Rule::in(['male', 'female'])],
            'birth_date' => ['nullable', 'date'],
            'class_id' => ['nullable', 'exists:classes,id'],
            'guardian_primary_id' => ['nullable', 'exists:guardians,id'],
            'address' => ['nullable', 'string'],
            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
        ]);

        $student->update($data);

        return response()->json($student->load(['classRoom', 'guardian']));
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return response()->noContent();
    }

    public function export(): BinaryFileResponse
    {
        return Excel::download(new StudentsExport, 'siswa_' . date('Y-m-d') . '.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        try {
            Excel::import(new StudentsImport, $request->file('file'));

            return response()->json([
                'message' => 'Data siswa berhasil diimport',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengimport data: ' . $e->getMessage(),
            ], 500);
        }
    }
}
