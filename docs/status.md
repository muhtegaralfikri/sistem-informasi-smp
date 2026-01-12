# Status Implementasi (Checkpoint)

## Sudah Selesai
- Setup Laravel 12 di root (tanpa subfolder), Breeze auth aktif (login/register/profile).
- Environment terhubung ke MySQL container (host `127.0.0.1`, port `3308`, DB `smp_db`, user `smp_user`).
- Migrasi & seeder dibuat dan sudah dijalankan:
  - RBAC dasar: `roles`, `users` (+ middleware `role`).
  - Data master inti: academic_years, semesters, classes, subjects, teachers, guardians, students, student_guardian, class_subjects.
  - Operasional inti: attendance_sheets/records, assessments/grade_entries, report_cards/report_card_items, announcements.
  - Seeder role + admin default (`admin@smp.test` / `admin123`).
- Dokumentasi pendukung: PRD, ERD/urutan migrasi (`docs/erd.md`), endpoint draft (`docs/endpoint.md`), checklist testing (`docs/test-checklist.md`).
- Build aset awal (Vite) sudah dijalankan saat install Breeze.

## Belum Selesai
- Implementasi controller/route/view untuk:
  - CRUD data master + penugasan guru (class_subjects).
  - Absensi (sheet, record, lock, rekap & ekspor).
  - Penilaian berbobot dan rekap nilai.
  - Raport: perhitungan nilai akhir, approval/publish, generate PDF.
  - Pengumuman & Portal Orang Tua (akses berdasarkan relasi student_guardian).
  - Dashboard eksekutif (statistik kehadiran/nilai).
- Policy/Gate detail per role; binding middleware `role` pada route.
- Import/Export CSV/Excel; audit log opsional; notifikasi (WA/email).
- UI kustom (masih template Breeze).
- Feature tests untuk alur bisnis (saat ini hanya test bawaan Breeze).

## Langkah Lanjut yang Disarankan
1) Tambah route + controller CRUD data master dan penugasan guru, pakai middleware `auth` + `role`.
2) Bangun alur absensi dan penilaian (form + rekap), validasi bobot = 100%.
3) Hitung raport, approval/publish, template PDF.
4) Portal orang tua dan dashboard eksekutif.
5) Tambah feature tests sesuai `docs/test-checklist.md`.
