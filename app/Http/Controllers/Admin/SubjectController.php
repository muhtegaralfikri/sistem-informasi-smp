<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index()
    {
        return view('admin.subjects.index', [
            'subjects' => Subject::orderBy('name')->paginate(15)
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:subjects,code'],
            'name' => ['required', 'string', 'max:150'],
            'passing_grade' => ['sometimes', 'integer', 'between:0,100'],
        ]);

        $subject = Subject::create($data);

        return response()->json($subject, 201);
    }

    public function show(Subject $subject)
    {
        return response()->json($subject);
    }

    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'code' => ['sometimes', 'string', 'max:20', Rule::unique('subjects', 'code')->ignore($subject->id)],
            'name' => ['sometimes', 'string', 'max:150'],
            'passing_grade' => ['sometimes', 'integer', 'between:0,100'],
        ]);

        $subject->update($data);

        return response()->json($subject);
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return response()->noContent();
    }
}
