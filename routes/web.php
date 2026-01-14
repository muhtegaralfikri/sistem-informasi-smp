<?php

use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\ClassScheduleController;
use App\Http\Controllers\Admin\ClassSubjectController;
use App\Http\Controllers\Admin\GuardianController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\AdminViewController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ParentPortalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportCardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'role:Admin TU'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/panel', [AdminViewController::class, 'dashboard'])->name('panel');

    // Import/Export routes MUST be defined BEFORE resource routes
    // to prevent {id} parameter from catching 'export' and 'import' as IDs
    Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
    Route::post('students/bulk-class-update', [StudentController::class, 'bulkUpdateClass'])->name('students.bulk-class-update');
    Route::get('students/export', [StudentController::class, 'export'])->name('students.export');
    Route::post('teachers/import', [TeacherController::class, 'import'])->name('teachers.import');
    Route::get('teachers/export', [TeacherController::class, 'export'])->name('teachers.export');
    Route::post('guardians/import', [GuardianController::class, 'import'])->name('guardians.import');
    Route::get('guardians/export', [GuardianController::class, 'export'])->name('guardians.export');

    // Resource routes (with {id} wildcards) AFTER specific routes
    Route::resource('academic-years', AcademicYearController::class)->except(['create', 'edit']);
    Route::resource('semesters', SemesterController::class)->except(['create', 'edit']);
    Route::resource('subjects', SubjectController::class)->except(['create', 'edit']);
    Route::resource('classes', SchoolClassController::class)->except(['create', 'edit']);
    Route::resource('teachers', TeacherController::class)->except(['create', 'edit']);
    Route::resource('guardians', GuardianController::class)->except(['create', 'edit']);
    Route::resource('students', StudentController::class)->except(['create', 'edit']);
    Route::resource('class-subjects', ClassSubjectController::class)->except(['create', 'edit']);

    // Views (frontend-first, backend wiring later)
    Route::get('/attendance', [AdminViewController::class, 'attendance'])->name('attendance');
    Route::get('/assessments/ui', [AdminViewController::class, 'assessments'])->name('assessments.ui');
    Route::get('/report-cards/ui', [AdminViewController::class, 'reportCards'])->name('report-cards.ui');
    Route::get('/announcements', [AdminViewController::class, 'announcements'])->name('announcements');
    Route::get('/parent-portal', [AdminViewController::class, 'parentPortalPreview'])->name('parent-portal');

    Route::get('/parent-portal', [AdminViewController::class, 'parentPortalPreview'])->name('parent-portal');

    // Atur Mapel Kelas (Class-Subject Assignment)
    Route::get('class-subjects/class/{classId}', [ClassSubjectController::class, 'getByClass']);
    Route::post('class-subjects', [ClassSubjectController::class, 'store'])->name('class-subjects.store');
    Route::delete('class-subjects/{classSubject}', [ClassSubjectController::class, 'destroy'])->name('class-subjects.destroy');

    // Jadwal Pelajaran
    Route::get('schedules', [ClassScheduleController::class, 'index'])->name('schedules.index');
    Route::get('schedules/class/{classId}', [ClassScheduleController::class, 'getSchedules']);
    Route::post('schedules', [ClassScheduleController::class, 'store'])->name('schedules.store');
    Route::delete('schedules/{schedule}', [ClassScheduleController::class, 'destroy'])->name('schedules.destroy');

    // Absensi
    Route::get('attendance/sheets', [AttendanceController::class, 'sheetsIndex']);
    Route::post('attendance/sheets', [AttendanceController::class, 'sheetsStore']);
    Route::get('attendance/sheets/{sheet}', [AttendanceController::class, 'sheetsShow']);
    Route::post('attendance/sheets/{sheet}/records', [AttendanceController::class, 'recordsUpsert']);
    Route::post('attendance/sheets/{sheet}/lock', [AttendanceController::class, 'sheetsLock']);
    Route::delete('attendance/sheets/{sheet}', [AttendanceController::class, 'sheetsDestroy']);

    // Penilaian
    Route::get('assessments', [AssessmentController::class, 'index']);
    Route::post('assessments', [AssessmentController::class, 'store']);
    Route::get('assessments/{assessment}', [AssessmentController::class, 'show']);
    Route::put('assessments/{assessment}', [AssessmentController::class, 'update']);
    Route::patch('assessments/{assessment}', [AssessmentController::class, 'update']);
    Route::post('assessments/{assessment}/grades', [AssessmentController::class, 'gradesUpsert']);
    Route::get('class-subjects/{classSubject}/assessments', [AssessmentController::class, 'byClassSubject']);
    Route::get('class-subjects/{classSubject}/final-scores', [AssessmentController::class, 'calculateFinalScores']);

    // Raport
    Route::get('report-cards', [ReportCardController::class, 'index']);
    Route::post('report-cards', [ReportCardController::class, 'store']);
    Route::get('report-cards/{reportCard}', [ReportCardController::class, 'show']);
    Route::put('report-cards/{reportCard}', [ReportCardController::class, 'update']);
    Route::patch('report-cards/{reportCard}', [ReportCardController::class, 'update']);
    Route::post('report-cards/{reportCard}/items', [ReportCardController::class, 'itemsUpsert']);
    Route::post('report-cards/{reportCard}/publish', [ReportCardController::class, 'publish']);
    Route::post('report-cards/{reportCard}/approve', [ReportCardController::class, 'approve']);
    Route::post('report-cards/{reportCard}/generate-pdf', [ReportCardController::class, 'generatePdf']);
    Route::get('report-cards/{reportCard}/download', [ReportCardController::class, 'downloadPdf']);
    Route::get('students/{student}/report-cards', [ReportCardController::class, 'byStudent']);
});

// Guru: absensi & penilaian (kelas-mapel yang diajar)
Route::middleware(['auth', 'verified', 'role:Guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Guru\GuruController::class, 'dashboard'])->name('dashboard');
    Route::get('/assessments', [AdminViewController::class, 'assessments'])->name('assessments.ui');
    Route::get('/attendance', [AdminViewController::class, 'attendance'])->name('attendance.ui');

    // Attendance API for Guru
    Route::get('attendance/sheets', [AttendanceController::class, 'sheetsIndex']);
    Route::post('attendance/sheets', [AttendanceController::class, 'sheetsStore']);
    Route::get('attendance/sheets/{sheet}', [AttendanceController::class, 'sheetsShow']);
    Route::post('attendance/sheets/{sheet}/records', [AttendanceController::class, 'recordsUpsert']);
    Route::post('attendance/sheets/{sheet}/lock', [AttendanceController::class, 'sheetsLock']);
    Route::delete('attendance/sheets/{sheet}', [AttendanceController::class, 'sheetsDestroy']);

    // Students API for loading students in attendance
    Route::get('students', [StudentController::class, 'index']);
});

// Wali Kelas: rekap absensi kelasnya, approval raport
Route::middleware(['auth', 'verified', 'role:Wali Kelas'])->prefix('wali')->name('wali.')->group(function () {
    Route::get('/dashboard', fn () => view('wali.dashboard'))->name('dashboard');
    Route::get('/report-cards', [AdminViewController::class, 'reportCards'])->name('report-cards.ui');
    Route::get('/attendance', [AdminViewController::class, 'attendance'])->name('attendance.ui');
});

// Kepala Sekolah: dashboard & reports
Route::middleware(['auth', 'verified', 'role:Kepala Sekolah'])->prefix('kepsek')->name('kepsek.')->group(function () {
    Route::get('/dashboard', [AdminViewController::class, 'dashboard'])->name('dashboard');
    Route::get('/report-cards', [AdminViewController::class, 'reportCards'])->name('report-cards');
});

// Orang Tua: portal orang tua
Route::middleware(['auth', 'verified', 'role:Orang Tua'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/dashboard', [ParentPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/students/{student}/attendance', [ParentPortalController::class, 'studentAttendance'])->name('student.attendance');
    Route::get('/students/{student}/report-cards', [ParentPortalController::class, 'studentReportCards'])->name('student.report-cards');
    Route::get('/students/{student}/report-cards/{reportCard}/download', [ParentPortalController::class, 'downloadReportCard'])->name('student.report-card.download');
    Route::get('/announcements', [ParentPortalController::class, 'announcements'])->name('announcements');
});

require __DIR__.'/auth.php';
