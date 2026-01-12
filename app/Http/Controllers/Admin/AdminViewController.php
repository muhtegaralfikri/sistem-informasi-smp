<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Guardian;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;

class AdminViewController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'stats' => [
                'students' => Student::count(),
                'teachers' => Teacher::count(),
                'classes' => SchoolClass::count(),
                'subjects' => Subject::count(),
                'guardians' => Guardian::count(),
            ],
            'years' => AcademicYear::orderByDesc('start_date')->get(),
            'semesters' => Semester::with('academicYear')->orderByDesc('start_date')->get(),
        ]);
    }
}
