<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index()
    {
        return view('admin.students.index', [
            'students' => Student::with(['classRoom', 'guardian'])->orderBy('full_name')->paginate(15),
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
}
