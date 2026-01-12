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
        $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'nip' => ['nullable', 'string', 'max:50', 'unique:teachers,nip'],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $teacher = \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            // Find Guru role or default to something safe (but likely exists)
            $role = \App\Models\Role::where('name', 'Guru')->first();
            
            // Create User
            $user = \App\Models\User::create([
                'name' => $request->full_name,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make('password'), // Default password
                'role_id' => $role?->id,
                'status' => 'active',
            ]);

            // Create Teacher
            return Teacher::create([
                'user_id' => $user->id,
                'full_name' => $request->full_name,
                'nip' => $request->nip,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status,
            ]);
        });

        return response()->json($teacher->load('user'), 201);
    }

    public function show(Teacher $teacher)
    {
        return response()->json($teacher->load('user'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $data = $request->validate([
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
