<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    public function index()
    {
        return view('admin.teachers.index', [
            'teachers' => Teacher::with('user')->orderBy('full_name')->paginate(15)
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id', 'unique:teachers,user_id'],
            'nip' => ['nullable', 'string', 'max:50', 'unique:teachers,nip'],
            'full_name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $teacher = Teacher::create($data);

        return response()->json($teacher->load('user'), 201);
    }

    public function show(Teacher $teacher)
    {
        return response()->json($teacher->load('user'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $data = $request->validate([
            'user_id' => ['sometimes', 'exists:users,id', Rule::unique('teachers', 'user_id')->ignore($teacher->id)],
            'nip' => ['nullable', 'string', 'max:50', Rule::unique('teachers', 'nip')->ignore($teacher->id)],
            'full_name' => ['sometimes', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
        ]);

        $teacher->update($data);

        return response()->json($teacher->load('user'));
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();

        return response()->noContent();
    }
}
