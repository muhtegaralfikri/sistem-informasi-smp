<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\GuardiansExport;
use App\Imports\GuardiansImport;
use App\Models\Guardian;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GuardianController extends Controller
{
    public function index(Request $request)
    {
        $query = Guardian::with(['user', 'students']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->wantsJson()) {
            return response()->json($query->orderBy('full_name')->limit(200)->get());
        }

        if ($request->ajax()) {
            return view('admin.guardians.partials.table', [
                'guardians' => $query->orderBy('full_name')->paginate(15)->withQueryString(),
            ]);
        }

        return view('admin.guardians.index', [
            'guardians' => $query->orderBy('full_name')->paginate(15)->withQueryString(),
        ]);
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

    public function export(): BinaryFileResponse
    {
        return Excel::download(new GuardiansExport, 'wali_' . date('Y-m-d') . '.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        try {
            set_time_limit(300); // Increase timeout for bulk import with password hashing
            Excel::import(new GuardiansImport, $request->file('file'));

            return response()->json([
                'message' => 'Data wali berhasil diimport',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengimport data: ' . $e->getMessage(),
            ], 500);
        }
    }
}
