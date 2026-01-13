<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSubject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassSubjectController extends Controller
{
    public function index()
    {
        return response()->json(
            ClassSubject::with(['classRoom', 'subject', 'teacher'])->orderBy('class_id')->get()
        );
    }

    public function getByClass($classId)
    {
        return response()->json(
            ClassSubject::with(['subject', 'teacher'])
                ->where('class_id', $classId)
                ->orderByDesc('id')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
        ]);

        $this->ensureUniqueAssignment($data['class_id'], $data['subject_id']);

        $classSubject = ClassSubject::create($data);

        return response()->json($classSubject->load(['classRoom', 'subject', 'teacher']), 201);
    }

    public function show(ClassSubject $classSubject)
    {
        return response()->json($classSubject->load(['classRoom', 'subject', 'teacher']));
    }

    public function update(Request $request, ClassSubject $classSubject)
    {
        $data = $request->validate([
            'class_id' => ['sometimes', 'exists:classes,id'],
            'subject_id' => ['sometimes', 'exists:subjects,id'],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
        ]);

        $newClassId = $data['class_id'] ?? $classSubject->class_id;
        $newSubjectId = $data['subject_id'] ?? $classSubject->subject_id;
        $this->ensureUniqueAssignment($newClassId, $newSubjectId, $classSubject->id);

        $classSubject->update($data);

        return response()->json($classSubject->load(['classRoom', 'subject', 'teacher']));
    }

    public function destroy(ClassSubject $classSubject)
    {
        $classSubject->delete();

        return response()->noContent();
    }

    private function ensureUniqueAssignment(int $classId, int $subjectId, ?int $ignoreId = null): void
    {
        $exists = ClassSubject::where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();

        if ($exists) {
            abort(422, 'Mapel sudah terdaftar pada kelas tersebut.');
        }
    }
}
