<?php

use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\ClassSubjectController;
use App\Http\Controllers\Admin\GuardianController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\ProfileController;
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
    Route::resource('academic-years', AcademicYearController::class)->except(['create', 'edit']);
    Route::resource('semesters', SemesterController::class)->except(['create', 'edit']);
    Route::resource('subjects', SubjectController::class)->except(['create', 'edit']);
    Route::resource('classes', SchoolClassController::class)->except(['create', 'edit']);
    Route::resource('teachers', TeacherController::class)->except(['create', 'edit']);
    Route::resource('guardians', GuardianController::class)->except(['create', 'edit']);
    Route::resource('students', StudentController::class)->except(['create', 'edit']);
    Route::resource('class-subjects', ClassSubjectController::class)->except(['create', 'edit']);
});

require __DIR__.'/auth.php';
