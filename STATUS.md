# Status Implementasi (Updated)

## Sudah Selesai
- Laravel 12 + Breeze auth (login/register/profile) berjalan; admin default `admin@smp.test` / `admin123`.
- DB MySQL container: host `127.0.0.1`, port `3308`, DB `smp_db`, user `smp_user` / `secret`; migrasi + seeder sudah jalan.
- RBAC dasar: tabel `roles`/`users`, middleware `role`, guard Admin TU dipakai di route admin.
- Migrasi inti tersedia (data master, absensi, penilaian, raport, pengumuman).
- CRUD Admin (JSON) untuk data master + penugasan: academic-years, semesters, subjects, classes, teachers, guardians, students, class-subjects.
- API Admin (JSON) untuk:
  - Absensi: list/buat sheet, upsert record, lock sheet.
  - Penilaian: CRUD assessment, upsert nilai per assessment.
  - Raport: create/update draft, upsert item mapel, publish.

## Belum Selesai
- Business logic lebih lengkap: perhitungan bobot total = 100%, agregasi nilai -> raport, validasi kelas/semester aktif.
- Flow peran non-Admin TU (Guru, Wali Kelas, Orang Tua) + Policy/Gate per role.
- Import/Export CSV/Excel; ekspor PDF raport; pengumuman, portal orang tua, dashboard eksekutif.
- Notifikasi (WA/email), audit log opsional, UI kustom, feature tests alur bisnis.

## Next Steps Disarankan
1) Tambah Policy/Gate per role dan rute khusus Guru/Wali/Ortu (pemisahan akses).
2) Implementasi perhitungan bobot nilai dan agregasi raport (final_score, average_score) + template PDF.
3) Endpoint/flow portal orang tua, pengumuman, dashboard rekap, serta ekspor laporan.
4) Import/Export CSV/Excel untuk data master dan nilai/absensi.
5) Tambah feature tests sesuai prioritas (auth/RBAC, absensi, penilaian, raport publish).
