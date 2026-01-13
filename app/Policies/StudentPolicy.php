<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StudentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin TU, Guru, Wali Kelas can view all
        return in_array($user->role?->name, ['Admin TU', 'Guru', 'Wali Kelas', 'Kepala Sekolah']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Student $student): bool
    {
        // Admin TU, Guru, Wali Kelas, Kepala Sekolah can view any student
        if (in_array($user->role?->name, ['Admin TU', 'Guru', 'Wali Kelas', 'Kepala Sekolah'])) {
            return true;
        }

        // Orang tua can only view their own children
        if ($user->role?->name === 'Orang Tua') {
            return $user->guardian?->students()->where('students.id', $student->id)->exists() ?? false;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role?->name === 'Admin TU';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Student $student): bool
    {
        return $user->role?->name === 'Admin TU';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Student $student): bool
    {
        return $user->role?->name === 'Admin TU';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Student $student): bool
    {
        return $user->role?->name === 'Admin TU';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Student $student): bool
    {
        return $user->role?->name === 'Admin TU';
    }

    /**
     * Determine whether the user can view the student's report cards.
     */
    public function viewReportCards(User $user, Student $student): bool
    {
        return $this->view($user, $student);
    }

    /**
     * Determine whether the user can view the student's attendance.
     */
    public function viewAttendance(User $user, Student $student): bool
    {
        return $this->view($user, $student);
    }
}
