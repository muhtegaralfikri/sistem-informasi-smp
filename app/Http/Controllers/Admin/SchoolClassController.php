<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SchoolClassController extends Controller
{
    public function index()
    {
        return view('admin.classes.index', [
            'classes' => SchoolClass::with(['homeroomTeacher', 'semester'])
                ->orderBy('grade_level')
                ->orderBy('name')
                ->paginate(15)
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'grade_level' => ['required', 'integer', 'between:1,12'],
            'major' => ['nullable', 'string', 'max:100'],
            'homeroom_teacher_id' => ['nullable', 'exists:teachers,id'],
            'semester_id' => ['nullable', 'exists:semesters,id'],
        ]);

        $class = SchoolClass::create($data);

        return response()->json($class, 201);
    }

    public function show(SchoolClass $class)
    {
        return response()->json($class->load(['homeroomTeacher', 'semester']));
    }

    public function update(Request $request, SchoolClass $class)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:100'],
            'grade_level' => ['sometimes', 'integer', 'between:1,12'],
            'major' => ['nullable', 'string', 'max:100'],
            'homeroom_teacher_id' => ['nullable', 'exists:teachers,id'],
            'semester_id' => ['nullable', 'exists:semesters,id'],
        ]);

        $class->update($data);

        return response()->json($class->load(['homeroomTeacher', 'semester']));
    }

    public function destroy(SchoolClass $class)
    {
        $class->delete();

        return response()->noContent();
    }
}
