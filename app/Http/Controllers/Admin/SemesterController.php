<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SemesterController extends Controller
{
    public function index()
    {
        return response()->json(
            Semester::with('academicYear')->orderByDesc('start_date')->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'name' => ['required', 'string', 'max:50', Rule::unique('semesters', 'name')->where('academic_year_id', $request->input('academic_year_id'))],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (! empty($data['is_active'])) {
            Semester::where('is_active', true)->update(['is_active' => false]);
            AcademicYear::where('id', $data['academic_year_id'])->update(['is_active' => true]);
        }

        $semester = Semester::create($data);

        return response()->json($semester, 201);
    }

    public function show(Semester $semester)
    {
        return response()->json($semester->load('academicYear'));
    }

    public function update(Request $request, Semester $semester)
    {
        $data = $request->validate([
            'academic_year_id' => ['sometimes', 'exists:academic_years,id'],
            'name' => ['sometimes', 'string', 'max:50', Rule::unique('semesters', 'name')->where(fn ($query) => $query->where('academic_year_id', $request->input('academic_year_id', $semester->academic_year_id)))->ignore($semester->id)],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (array_key_exists('is_active', $data) && $data['is_active']) {
            Semester::where('id', '!=', $semester->id)->update(['is_active' => false]);
            AcademicYear::where('id', $data['academic_year_id'] ?? $semester->academic_year_id)->update(['is_active' => true]);
        }

        $semester->update($data);

        return response()->json($semester->fresh('academicYear'));
    }

    public function destroy(Semester $semester)
    {
        $semester->delete();

        return response()->noContent();
    }
}
