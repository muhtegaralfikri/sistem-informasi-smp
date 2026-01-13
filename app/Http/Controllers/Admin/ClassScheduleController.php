<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\ClassSubject;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class ClassScheduleController extends Controller
{
    public function index()
    {
        return view('admin.schedules.index', [
            'classes' => SchoolClass::orderBy('name')->get()
        ]);
    }

    public function getSchedules($classId)
    {
        $schedules = ClassSubject::where('class_id', $classId)
            ->with(['subject', 'teacher', 'schedules' => function($q) {
                $q->orderBy('day')->orderBy('start_time');
            }])
            ->get()
            ->map(function ($cs) {
                return [
                    'class_subject_id' => $cs->id,
                    'subject_name' => $cs->subject->name,
                    'teacher_name' => $cs->teacher ? $cs->teacher->full_name : 'Belum ada guru',
                    'schedules' => $cs->schedules
                ];
            });

        return response()->json($schedules);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'class_subject_id' => ['required', 'exists:class_subjects,id'],
            'day' => ['required', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'start_time' => ['required', 'date_format:H:i'],
            'duration_jp' => ['required', 'integer', 'min:1'],
        ]);

        // Calculate end time: start_time + (duration_jp * 40 minutes)
        $start = \Carbon\Carbon::createFromFormat('H:i', $data['start_time']);
        $end = $start->copy()->addMinutes($data['duration_jp'] * 40);

        $schedule = ClassSchedule::create([
            'class_subject_id' => $data['class_subject_id'],
            'day' => $data['day'],
            'start_time' => $data['start_time'],
            'end_time' => $end->format('H:i'),
        ]);

        return response()->json($schedule);
    }

    public function destroy(ClassSchedule $schedule)
    {
        $schedule->delete();
        return response()->noContent();
    }
}
