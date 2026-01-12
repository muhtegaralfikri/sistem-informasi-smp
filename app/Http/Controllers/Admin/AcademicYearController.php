<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcademicYearController extends Controller
{
    public function index()
    {
        return response()->json(
            AcademicYear::orderByDesc('start_date')->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:academic_years,name'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (! empty($data['is_active'])) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        $year = AcademicYear::create($data);

        return response()->json($year, 201);
    }

    public function show(AcademicYear $academicYear)
    {
        return response()->json($academicYear);
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:100', Rule::unique('academic_years', 'name')->ignore($academicYear->id)],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (array_key_exists('is_active', $data) && $data['is_active']) {
            AcademicYear::where('id', '!=', $academicYear->id)->update(['is_active' => false]);
        }

        $academicYear->update($data);

        return response()->json($academicYear);
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();

        return response()->noContent();
    }
}
