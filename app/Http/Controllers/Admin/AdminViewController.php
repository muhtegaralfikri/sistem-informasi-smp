<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Assessment;
use App\Models\AttendanceSheet;
use App\Models\ClassSubject;
use App\Models\SchoolClass;
use App\Models\ReportCard;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

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
            ],
            'years' => AcademicYear::orderByDesc('start_date')->get(),
            'semesters' => Semester::with('academicYear')->orderByDesc('start_date')->get(),
        ]);
    }

    public function attendance()
    {
        [$classes, $subjects, $teachers, $students, $sheets] = $this->attendanceScopedData();

        // Pre-map sheets to avoid multi-line closure issue in Blade
        $sheetsData = $sheets->map(fn($s) => [
            'id' => $s->id,
            'class' => $s->classRoom->name ?? '-',
            'class_id' => $s->class_id,
            'subject' => $s->subject->name ?? '-',
            'teacher' => $s->teacher->full_name ?? '-',
            'date' => optional($s->date)->format('Y-m-d'),
            'session' => $s->session,
            'locked' => !is_null($s->locked_at),
        ]);

        // Determine API base URL based on role
        $role = Auth::user()?->role?->name;
        $apiBaseUrl = match ($role) {
            'Guru' => '/guru',
            'Wali Kelas' => '/wali',
            default => '/admin',
        };

        return view('admin.attendance.index', [
            'classes' => $classes,
            'subjects' => $subjects,
            'teachers' => $teachers,
            'students' => $students,
            'sheets' => $sheets,
            'sheetsData' => $sheetsData,
            'apiBaseUrl' => $apiBaseUrl,
        ]);
    }

    public function assessments()
    {
        $role = Auth::user()?->role?->name;
        $teacher = $this->currentTeacher();
        $assessmentQuery = Assessment::with(['classSubject.classRoom', 'classSubject.subject'])
            ->orderByDesc('created_at');

        if ($role === 'Guru' && $teacher) {
            $assessmentQuery->whereHas('classSubject', fn ($q) => $q->where('teacher_id', $teacher->id));
        }

        $assessments = $assessmentQuery->limit(12)->get();

        $weightStats = $assessments
            ->groupBy('class_subject_id')
            ->map(function (Collection $group) {
                $first = $group->first();
                return [
                    'class_subject_id' => $first?->class_subject_id,
                    'mapel' => trim(($first?->classSubject?->subject?->name ?? 'Mapel') . ' (' . ($first?->classSubject?->classRoom?->name ?? 'Kelas') . ')'),
                    'total' => (int) $group->sum('weight'),
                    'items' => $group->map(fn ($a) => [
                        'type' => $a->type,
                        'weight' => $a->weight,
                    ])->values(),
                ];
            })
            ->values()
            ->toArray();

        $assessmentPayload = $assessments->map(function ($a) {
            return [
                'id' => $a->id,
                'class' => $a->classSubject?->classRoom?->name ?? '-',
                'subject' => $a->classSubject?->subject?->name ?? '-',
                'title' => $a->title,
                'type' => $a->type,
                'weight' => $a->weight,
                'max_score' => $a->max_score,
                'due_date' => optional($a->due_date)->format('Y-m-d'),
                'progress' => 0,
                'status' => 'Belum diisi',
            ];
        })->values()->toArray();

        $gradeInputs = Student::select('id', 'full_name', 'nis')->orderBy('full_name')->limit(15)->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->full_name,
                'nis' => $s->nis,
                'score' => null,
                'note' => '',
            ])
            ->values()
            ->toArray();

        return view('admin.assessments.index', [
            'assessments' => $assessmentPayload,
            'weightStats' => $weightStats,
            'gradeInputs' => $gradeInputs,
        ]);
    }

    public function reportCards()
    {
        $role = Auth::user()?->role?->name;
        $teacher = $this->currentTeacher();

        $reportQuery = ReportCard::with(['student.classRoom.homeroomTeacher', 'semester'])
            ->orderByDesc('created_at');

        if ($role === 'Wali Kelas' && $teacher) {
            $reportQuery->whereHas('student.classRoom', fn ($q) => $q->where('homeroom_teacher_id', $teacher->id));
        }

        $reports = $reportQuery->limit(50)->get();

        $reportPayload = $reports->map(function ($r) {
            return [
                'id' => $r->id,
                'student' => $r->student?->full_name ?? '-',
                'class' => $r->student?->classRoom?->name ?? '-',
                'status' => $r->status,
                'average_score' => $r->average_score,
            ];
        })->values()->toArray();

        $classRecap = $reports
            ->groupBy(fn ($r) => $r->student?->classRoom?->name ?? 'Tanpa Kelas')
            ->map(function (Collection $group, $className) {
                return [
                    'class' => $className,
                    'homeroom' => $group->first()?->student?->classRoom?->homeroomTeacher?->full_name ?? '-',
                    'draft' => $group->where('status', 'draft')->count(),
                    'approved' => $group->where('status', 'approved')->count(),
                    'published' => $group->where('status', 'published')->count(),
                    'avg' => round($group->avg('average_score') ?? 0),
                ];
            })
            ->values();

        return view('admin.report-cards.index', [
            'reports' => $reportPayload,
            'classRecap' => $classRecap,
        ]);
    }

    public function announcements()
    {
        return view('admin.announcements.index', [
            'announcements' => Announcement::with('targetClass')
                ->orderByDesc('published_at')
                ->orderByDesc('created_at')
                ->limit(15)
                ->get()
                ->map(function ($a) {
                    return [
                        'id' => $a->id,
                        'title' => $a->title,
                        'target' => $a->target_scope === 'class'
                            ? 'Kelas ' . ($a->targetClass?->name ?? '')
                            : ($a->target_scope === 'parents' ? 'Orang Tua' : 'Semua'),
                        'published_at' => $a->published_at ? 'Diterbitkan ' . $a->published_at->format('d M Y') : 'Draft',
                        'body' => $a->body,
                    ];
                })
                ->values()
                ->toArray(),
            'classes' => SchoolClass::orderBy('grade_level')->orderBy('name')->get(),
        ]);
    }

    public function parentPortalPreview()
    {
        $student = Student::with(['classRoom', 'guardian'])->orderBy('full_name')->first();

        return view('admin.parents.index', [
            'student' => $student ? [
                'id' => $student->id,
                'name' => $student->full_name,
                'class' => $student->classRoom?->name,
            ] : null,
            'reports' => ReportCard::with('semester')
                ->where('student_id', $student?->id)
                ->orderByDesc('created_at')
                ->limit(3)
                ->get()
                ->map(fn ($r) => [
                    'id' => $r->id,
                    'semester' => $r->semester?->name ?? '',
                    'status' => $r->status,
                    'average' => $r->average_score,
                    'published_at' => optional($r->published_at)->format('Y-m-d'),
                ])
                ->values()
                ->toArray(),
            'announcements' => Announcement::orderByDesc('created_at')
                ->limit(5)
                ->get()
                ->map(fn ($a) => [
                    'title' => $a->title,
                    'body' => $a->body,
                    'date' => optional($a->published_at ?? $a->created_at)->format('d M Y'),
                ])
                ->values()
                ->toArray(),
        ]);
    }

    private function currentTeacher(): ?Teacher
    {
        $userId = Auth::id();
        if (!$userId) {
            return null;
        }
        return Teacher::where('user_id', $userId)->first();
    }

    private function attendanceScopedData(): array
    {
        $role = Auth::user()?->role?->name;
        $teacher = $this->currentTeacher();

        $classesQuery = SchoolClass::with('homeroomTeacher')->orderBy('grade_level')->orderBy('name');
        $subjectsQuery = Subject::orderBy('name');
        $teachersQuery = Teacher::orderBy('full_name');
        $studentsQuery = Student::orderBy('full_name');
        $sheetsQuery = AttendanceSheet::with(['classRoom', 'subject', 'teacher'])->orderByDesc('date')->orderByDesc('created_at');

        if ($role === 'Guru' && $teacher) {
            $classesQuery->whereHas('classSubjects', fn ($q) => $q->where('teacher_id', $teacher->id));
            $subjectsQuery->whereIn('id', ClassSubject::where('teacher_id', $teacher->id)->pluck('subject_id'));
            $teachersQuery->where('id', $teacher->id);
            $studentsQuery->whereHas('classRoom', fn ($q) => $q->whereHas('classSubjects', fn ($cq) => $cq->where('teacher_id', $teacher->id)));
            $sheetsQuery->where('teacher_id', $teacher->id);
        }

        if ($role === 'Wali Kelas' && $teacher) {
            $classesQuery->where('homeroom_teacher_id', $teacher->id);
            $subjectsQuery->whereIn('id', ClassSubject::whereHas('classRoom', fn ($q) => $q->where('homeroom_teacher_id', $teacher->id))->pluck('subject_id'));
            $teachersQuery->where('id', $teacher->id);
            $studentsQuery->whereHas('classRoom', fn ($q) => $q->where('homeroom_teacher_id', $teacher->id));
            $sheetsQuery->whereHas('classRoom', fn ($q) => $q->where('homeroom_teacher_id', $teacher->id));
        }

        return [
            $classesQuery->get(),
            $subjectsQuery->get(),
            $teachersQuery->get(),
            $studentsQuery->limit(50)->get(),
            $sheetsQuery->limit(10)->get(),
        ];
    }
}
