<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\ClassSubject;
use App\Models\ClassSchedule;
use App\Models\AttendanceSheet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class GuruController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return view('guru.dashboard', [
                'teacher' => null,
                'classSubjects' => collect(),
                'todaySchedules' => collect(),
                'attendanceCount' => 0,
                'pendingAssessments' => 0,
            ]);
        }

        // Get teacher's class-subject assignments
        $classSubjects = ClassSubject::with(['classRoom', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->get();

        // Get today's schedules
        $today = strtolower(Carbon::now()->format('l')); // monday, tuesday, etc.
        $todaySchedules = ClassSchedule::with(['classSubject.classRoom', 'classSubject.subject'])
            ->whereHas('classSubject', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->where('day', $today)
            ->orderBy('start_time')
            ->get();

        // Count attendance sheets created by this teacher
        $attendanceCount = AttendanceSheet::where('teacher_id', $teacher->id)->count();

        // Pending assessments placeholder (could be expanded later)
        $pendingAssessments = 0;

        return view('guru.dashboard', [
            'teacher' => $teacher,
            'classSubjects' => $classSubjects,
            'todaySchedules' => $todaySchedules,
            'attendanceCount' => $attendanceCount,
            'pendingAssessments' => $pendingAssessments,
        ]);
    }
}
