<?php

use App\Http\Controllers\Admin\AcademicYearController;
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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportCardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'role:Admin TU'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/panel', [AdminViewController::class, 'dashboard'])->name('panel');
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

    // Absensi
    Route::get('attendance/sheets', [AttendanceController::class, 'sheetsIndex']);
    Route::post('attendance/sheets', [AttendanceController::class, 'sheetsStore']);
    Route::get('attendance/sheets/{sheet}', [AttendanceController::class, 'sheetsShow']);
    Route::post('attendance/sheets/{sheet}/records', [AttendanceController::class, 'recordsUpsert']);
    Route::post('attendance/sheets/{sheet}/lock', [AttendanceController::class, 'sheetsLock']);

    // Penilaian
    Route::get('assessments', [AssessmentController::class, 'index']);
    Route::post('assessments', [AssessmentController::class, 'store']);
    Route::get('assessments/{assessment}', [AssessmentController::class, 'show']);
    Route::put('assessments/{assessment}', [AssessmentController::class, 'update']);
    Route::patch('assessments/{assessment}', [AssessmentController::class, 'update']);
    Route::post('assessments/{assessment}/grades', [AssessmentController::class, 'gradesUpsert']);

    // Raport
    Route::get('report-cards', [ReportCardController::class, 'index']);
    Route::post('report-cards', [ReportCardController::class, 'store']);
    Route::get('report-cards/{reportCard}', [ReportCardController::class, 'show']);
    Route::put('report-cards/{reportCard}', [ReportCardController::class, 'update']);
    Route::patch('report-cards/{reportCard}', [ReportCardController::class, 'update']);
    Route::post('report-cards/{reportCard}/items', [ReportCardController::class, 'itemsUpsert']);
    Route::post('report-cards/{reportCard}/publish', [ReportCardController::class, 'publish']);
});

require __DIR__.'/auth.php';
