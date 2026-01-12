<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GuardianController extends Controller
{
    public function index()
    {
        return response()->json(Guardian::with('user')->orderBy('full_name')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id', 'unique:guardians,user_id'],
            'full_name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'relation_default' => ['nullable', 'string', 'max:50'],
        ]);

        $guardian = Guardian::create($data);

        return response()->json($guardian->load('user'), 201);
    }

    public function show(Guardian $guardian)
    {
        return response()->json($guardian->load('user'));
    }

    public function update(Request $request, Guardian $guardian)
    {
        $data = $request->validate([
            'user_id' => ['sometimes', 'exists:users,id', Rule::unique('guardians', 'user_id')->ignore($guardian->id)],
            'full_name' => ['sometimes', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'relation_default' => ['nullable', 'string', 'max:50'],
        ]);

        $guardian->update($data);

        return response()->json($guardian->load('user'));
    }

    public function destroy(Guardian $guardian)
    {
        $guardian->delete();

        return response()->noContent();
    }
}
