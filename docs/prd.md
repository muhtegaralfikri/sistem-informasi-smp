# Product Requirements Document (PRD)
## Sistem Informasi Operasional SMP Terpadu

Dokumen ini memfokuskan rincian implementasi: model data, alur pengguna, workflow pengembangan, dan kebutuhan teknis untuk membangun sistem berbasis Laravel + MySQL.

---

## 1) Cakupan Modul (MVP)
- Auth & RBAC: login, manajemen pengguna/role, reset password.
- Data Akademik: tahun ajaran, semester, kelas, jurusan (opsional), mata pelajaran, guru, wali kelas, siswa, orang tua/wali.
- Penugasan Mengajar: guru ↔ kelas ↔ mapel.
- Absensi: input harian per kelas/mapel, rekap, ekspor.
- Penilaian: bobot nilai per mapel (Tugas, Ulangan Harian, UTS, UAS), input nilai, rekap.
- Raport Digital: generate PDF, arsip per semester, persetujuan wali kelas.
- Portal Orang Tua: absensi, nilai, raport, pengumuman.
- Dashboard: statistik siswa/guru, kehadiran hari ini, rata-rata nilai, siswa bermasalah.
- Pengumuman: kirim ke semua/kelas tertentu/orang tua.
- Ekspor/Impor: CSV/Excel untuk data master; PDF/Excel untuk laporan.

---

## 2) Arsitektur Aplikasi
- Backend: Laravel (MVC), middleware RBAC per role, form request validation, job queue opsional (notifikasi WA).
- Frontend: Blade + Bootstrap/Tailwind, komponen tabel/filter/ekspor.
- Database: MySQL; migration + seeder; indeks di kolom pencarian (nis, nama, kelas, tanggal).
- File: storage untuk raport PDF; symlink `storage:link`.
- Deployment: Apache/Nginx + PHP-FPM; .env untuk DB/Mail/WA gateway (opsional).

---

## 3) Model Data & Skema Tabel (inti)
Gunakan konvensi Laravel: `id` bigint unsigned AI, `created_at/updated_at`, `deleted_at` untuk soft delete bila perlu. Berikut tabel utama (ringkas):

### 3.1 Identitas & RBAC
- `roles` (id, name, permissions_json, is_system)
- `users` (id, name, email, phone, password_hash, role_id, status[active|inactive], last_login_at)
- `password_resets` (email, token, created_at) – built-in
- (Opsional) `audit_logs` (id, user_id, action, model, model_id, payload_json, ip, ua)

### 3.2 Akademik & Organisasi
- `academic_years` (id, name, start_date, end_date, is_active)
- `semesters` (id, academic_year_id, name[Ganjil/Genap], start_date, end_date, is_active)
- `classes` (id, name, grade_level, major, homeroom_teacher_id, semester_id current)
- `subjects` (id, code, name, passing_grade)
- `class_subjects` (id, class_id, subject_id, teacher_id) – penugasan guru per kelas-mapel
- `teachers` (id, user_id, nip, full_name, phone, email, status)
- `guardians` (id, user_id, full_name, phone, email, relation_default)
- `students` (id, nis, nisn, full_name, gender, birth_date, class_id, guardian_primary_id, address, status)
- `student_guardian` (student_id, guardian_id, relation) – untuk >1 wali

### 3.3 Absensi
- `attendance_sheets` (id, class_id, subject_id, teacher_id, semester_id, date, session, locked_at)
- `attendance_records` (id, attendance_sheet_id, student_id, status[Hadir|Izin|Sakit|Alfa], note)
Indeks: (class_id, date), (student_id, date), (subject_id, date).

### 3.4 Penilaian & Raport
- `assessments` (id, class_subject_id, semester_id, type[Tugas|UH|UTS|UAS], title, weight, max_score, due_date)
- `grade_entries` (id, assessment_id, student_id, score)
- `report_cards` (id, student_id, semester_id, status[draft|approved|published], total_score, average_score, remarks, approved_by, pdf_path, published_at)
- `report_card_items` (id, report_card_id, subject_id, final_score, predicate, notes)
Perhitungan nilai: bobot per assessment type; final_score per mapel dihitung dari weighted score; aggregate untuk raport.

### 3.5 Pengumuman & Notifikasi
- `announcements` (id, title, body, target_scope[all|class|parents], class_id nullable, published_at, author_id)
- (Opsional) `notifications` (id, user_id, channel[email|wa], title, body, status)

---

## 4) Alur Pengguna (tingkat sistem)
- **Setup awal (Admin TU):** buat tahun ajaran + semester aktif → buat kelas & wali kelas → buat mapel → assign guru per kelas-mapel → impor siswa + orang tua → generate akun pengguna.
- **Absensi (Guru/Wali Kelas):** pilih kelas + mapel + tanggal → isi status default Hadir → simpan → sheet terkunci opsional → rekap otomatis tersedia (harian/mingguan/bulanan) → ekspor PDF/Excel.
- **Penilaian (Guru):** buat assessment (type, bobot, max score) per kelas-mapel → input nilai per siswa → sistem hitung skor berbobot → tampilkan rekap nilai & rata-rata.
- **Validasi Raport (Wali Kelas):** cek rekap absensi + nilai → beri catatan → set status approved → trigger generate PDF → simpan di storage → status published (akses orang tua).
- **Portal Orang Tua:** login → lihat absensi anak, nilai, raport PDF → baca pengumuman.
- **Dashboard (Kepala Sekolah/Admin):** statistik siswa/guru, kehadiran hari ini, grafik tren absensi, rata-rata nilai per mapel, list siswa bermasalah.

---

## 5) Pemetaan Layar/Route (Ringkas)
- Auth: login, lupa password.
- Admin TU:
  - Tahun ajaran & semester (CRUD + set aktif)
  - Kelas & wali kelas, mapel, penugasan guru
  - Data siswa, guru, orang tua (impor/ekspor CSV/Excel)
  - User & role
  - Rekap & ekspor absensi/nilai
- Guru:
  - Dashboard pribadi (kelas-mapel yang diajar)
  - Absensi per kelas/mapel
  - Daftar assessment & input nilai
  - Rekap nilai per kelas-mapel
- Wali Kelas:
  - Rekap absensi kelas
  - Validasi raport, catatan siswa
  - Pengesahan PDF raport
- Kepala Sekolah:
  - Dashboard eksekutif
  - Laporan kehadiran dan nilai agregat
- Orang Tua:
  - Ringkasan anak (absensi, nilai, raport PDF)
  - Pengumuman

---

## 6) API/Route Teknis (contoh pola REST Laravel)
- Prefix web (Blade): resource controller dengan middleware role.
- Prefix API (jika diperlukan SPA/mobile):
  - `POST /api/login`, `POST /api/logout`
  - `GET /api/semesters/active`
  - `GET/POST /api/classes`, `GET /api/classes/{id}/students`
  - `GET/POST /api/class-subjects`
  - `GET/POST /api/attendance/sheets`, `POST /api/attendance/sheets/{id}/records`
  - `GET/POST /api/assessments`, `POST /api/assessments/{id}/grades`
  - `POST /api/report-cards/{id}/approve`, `POST /api/report-cards/{id}/publish`
  - `GET /api/parents/{id}/children/{studentId}/summary`
Gunakan FormRequest untuk validasi; policy untuk akses per role.

---

## 7) Aturan Bisnis Utama
- Satu semester aktif per tahun ajaran; hanya satu semester aktif global pada satu waktu.
- Siswa harus terkait ke satu kelas aktif; riwayat pindah kelas dapat dicatat di log/riwayat terpisah (opsional).
- Bobot nilai per mapel harus berjumlah 100% (validasi saat set bobot).
- Absensi: status default Hadir; sheet dapat dikunci agar tidak diedit setelah disahkan.
- Raport hanya bisa dipublish jika status approved oleh wali kelas.
- Orang tua hanya bisa melihat data siswa yang terkait di `student_guardian`.

---

## 8) Rencana Pengembangan (17 hari sesuai README, dipecah teknis)
1. **Setup & Auth (2 hari):** skeleton Laravel, konfigurasi .env, role seeder, login/logout, reset password, middleware RBAC.
2. **Data Master (3 hari):** akademik (tahun ajaran/semester), kelas + wali kelas, mapel, guru, siswa, orang tua, impor CSV/Excel.
3. **Penugasan Guru (0.5 hari):** class_subjects dan UI assign.
4. **Absensi (2.5 hari):** sheet + record, filter, rekap, ekspor PDF/Excel.
5. **Penilaian (3 hari):** definisi assessment + bobot, input nilai, rekap berbobot.
6. **Raport (2 hari):** generate final score, approval wali kelas, PDF template, arsip.
7. **Portal Orang Tua (1.5 hari):** dashboard orang tua, akses absensi/nilai/raport, pengumuman.
8. **Dashboard (1.5 hari):** statistik eksekutif, grafik tren absensi & nilai.
9. **Testing & Hardening (1 hari):** feature tests utama, validasi file impor, role access checks.

---

## 9) Testing & Quality
- Feature tests: auth, RBAC per role, impor siswa/guru, absensi sheet, input nilai, approval/publish raport, akses orang tua.
- Validation: sanitasi file CSV/Excel (ukuran, header, tipe data), rentang nilai 0–100, bobot total 100.
- PDF: snapshot visual sederhana (periksa field wajib terisi).
- Performance: indeks di nis, class_id, subject_id, date; eager loading pada rekap.

---

## 10) Deployment & Operasional
- Environment: PHP 8.x, MySQL 8.x, ext gd/imagick (untuk PDF bila perlu).
- `.env` contoh: DB_*, MAIL_*, FILESYSTEM_DRIVER, WA_GATEWAY_URL/API_KEY (opsional), APP_URL.
- Perintah: `composer install`, `php artisan key:generate`, `php artisan migrate --seed`, `php artisan storage:link`.
- Backup: dump DB terjadwal, backup storage/raport PDF per semester.
- Logging: Laravel log + audit log opsional untuk aksi penting (create/update/delete pada absensi, nilai, raport).

---

## 11) Acceptance Criteria (MVP)
- Semua role hanya melihat fitur sesuai tabel RBAC.
- Admin TU dapat set semester aktif, membuat kelas/mapel/guru/siswa/orang tua, impor CSV/Excel tanpa error.
- Guru dapat membuat assessment, input nilai, dan sheet absensi; rekap berbobot tampil benar.
- Wali kelas dapat menyetujui raport dan sistem menghasilkan PDF tersimpan di storage.
- Orang tua dapat login dan mengakses absensi/nilai/raport anaknya.
- Dashboard menampilkan data aktual hari ini (kehadiran) dan rata-rata nilai per mapel.

---

## 12) Backlog Pasca-MVP
- Absensi QR code, WhatsApp Gateway notifikasi, audit log penuh, multi tahun ajaran paralel, sistem SPP sederhana, pengajuan izin online.
