# ERD & Rencana Migrasi (MVP)

## Diagram Teks (ringkas)
```
roles --< users
academic_years --< semesters --< classes --< students
classes --< class_subjects >-- subjects
class_subjects --< assessments --< grade_entries
students --< attendance_records >-- attendance_sheets >-- classes
students --< report_card_items >-- report_cards --< semesters
students >-- student_guardian --< guardians
users --< teachers --< class_subjects
users --< guardians --< student_guardian
users --< announcements
```

## Daftar Tabel & Kolom Utama
- `roles` (name, permissions_json, is_system)
- `users` (name, email, phone, password, role_id, status, last_login_at)
- `academic_years` (name, start_date, end_date, is_active)
- `semesters` (academic_year_id, name, start_date, end_date, is_active)
- `classes` (name, grade_level, major, homeroom_teacher_id, semester_id)
- `subjects` (code, name, passing_grade)
- `class_subjects` (class_id, subject_id, teacher_id)
- `teachers` (user_id, nip, full_name, phone, email, status)
- `guardians` (user_id, full_name, phone, email, relation_default)
- `students` (nis, nisn, full_name, gender, birth_date, class_id, guardian_primary_id, address, status)
- `student_guardian` (student_id, guardian_id, relation)
- `attendance_sheets` (class_id, subject_id, teacher_id, semester_id, date, session, locked_at)
- `attendance_records` (attendance_sheet_id, student_id, status, note)
- `assessments` (class_subject_id, semester_id, type, title, weight, max_score, due_date)
- `grade_entries` (assessment_id, student_id, score)
- `report_cards` (student_id, semester_id, status, total_score, average_score, remarks, approved_by, pdf_path, published_at)
- `report_card_items` (report_card_id, subject_id, final_score, predicate, notes)
- `announcements` (title, body, target_scope, class_id, published_at, author_id)
- (Opsional) `notifications` (user_id, channel, title, body, status)
- (Opsional) `audit_logs` (user_id, action, model, model_id, payload_json, ip, ua)

## Urutan Migrasi yang Disarankan
1) roles, users, password_resets (bawaan)
2) academic_years, semesters
3) classes, subjects, teachers, guardians, students, student_guardian
4) class_subjects (relasi guru-mapel-kelas)
5) attendance_sheets, attendance_records
6) assessments, grade_entries
7) report_cards, report_card_items
8) announcements (± notifications)
9) audit_logs (opsional)

## Indeks & Integritas
- Indeks pencarian: `students.nis`, `students.nisn`, `classes.name`, `subjects.code`, `attendance_sheets(class_id,date)`, `attendance_records(student_id,status)`, `grade_entries(student_id)`.
- Foreign key dengan `on update cascade`, `on delete restrict/set null` sesuai kebutuhan (contoh: `class_id` pada students `restrict`, `guardian_primary_id` `set null`).
- Unique: `subjects.code`, kombinasi `class_subjects (class_id, subject_id)`; `students.nis`, `students.nisn`; `users.email`.

## Validasi Bisnis di Level DB (opsional)
- Check constraint untuk nilai: `grade_entries.score` 0–100.
- Check constraint bobot: enforce di aplikasi agar total bobot 100% per mapel/kelas.

