# Daftar Endpoint (Draft MVP)

## Web (Blade, middleware auth + role)
- `GET /` — dashboard sesuai role.
- `GET /login` / `POST /login` — autentikasi.
- `POST /logout` — keluar.
  - Payload login: `email`, `password`, `remember` (opsional).
  - Akun awal untuk tes: `admin@smp.test` / `admin123` (dari seeder).

### Admin TU
- `resource /academic-years` — CRUD tahun ajaran, set aktif.
- `resource /semesters` — CRUD semester, set aktif.
- `resource /classes` — CRUD kelas (+ wali kelas).
- `resource /subjects` — CRUD mapel.
- `resource /teachers` — CRUD guru.
- `resource /guardians` — CRUD orang tua/wali.
- `resource /students` — CRUD siswa (+ assign wali).
- `POST /imports/{type}` — impor CSV/Excel (siswa/guru/kelas/mapel).
- `resource /users` — CRUD user + role.
- `resource /class-subjects` — penugasan guru ke kelas–mapel.
- `GET /reports/attendance` — rekap absensi (filter + ekspor).
- `GET /reports/grades` — rekap nilai (filter + ekspor).

### Guru
- `GET /dashboard/teacher` — ringkasan kelas–mapel.
- `resource /attendance/sheets` — buat/list sheet absensi per kelas–mapel.
- `POST /attendance/sheets/{sheet}/lock` — kunci sheet.
- `POST /attendance/sheets/{sheet}/records` — input/update status siswa.
- `resource /assessments` — CRUD assessment per kelas–mapel.
- `POST /assessments/{assessment}/grades` — input nilai per siswa.

### Wali Kelas
- `GET /homeroom/attendance` — rekap absensi kelas.
- `GET /homeroom/report-cards` — daftar raport kelas.
- `POST /report-cards/{id}/approve` — persetujuan raport.
- `POST /report-cards/{id}/publish` — publish + simpan PDF.

### Kepala Sekolah
- `GET /dashboard/executive` — statistik siswa/guru, kehadiran hari ini, tren nilai/absensi.

### Orang Tua
- `GET /parent/children` — daftar anak.
- `GET /parent/children/{id}/attendance` — absensi anak.
- `GET /parent/children/{id}/grades` — nilai anak.
- `GET /parent/children/{id}/report-cards` — daftar raport + unduh PDF.

### Pengumuman
- `resource /announcements` — CRUD + publish; target_scope all/class/parents.

---

## API (opsional, JSON; prefix `/api`, middleware auth:api + role)
- `POST /api/login`, `POST /api/logout`
- `GET /api/semesters/active`
- `GET /api/classes`, `GET /api/classes/{id}/students`
- `GET/POST /api/class-subjects`
- `GET/POST /api/attendance/sheets`, `POST /api/attendance/sheets/{id}/records`, `POST /api/attendance/sheets/{id}/lock`
- `GET/POST /api/assessments`, `POST /api/assessments/{id}/grades`
- `POST /api/report-cards/{id}/approve`, `POST /api/report-cards/{id}/publish`
- `GET /api/parents/{id}/children/{studentId}/summary`

Catatan: detail payload/response mengikuti konvensi REST Laravel; gunakan Form Request untuk validasi dan Policy/Gate untuk kontrol akses per role.
