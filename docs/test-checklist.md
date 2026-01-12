# Checklist Pengujian (MVP)

## Otentikasi & RBAC
- Login / logout, reset password (email valid / invalid).
- Session & middleware `role`: tiap role hanya bisa mengakses menu yang diperbolehkan.
- Akun non-aktif (`status=inactive`) ditolak.

## Data Master & Import
- CRUD tahun ajaran, semester (hanya satu `is_active=true`).
- CRUD kelas + wali kelas, mapel, guru, siswa, orang tua; validasi unik `nis/nisn` dan `subjects.code`.
- Import CSV/Excel untuk siswa/guru; tolak ukuran/format salah; rollback jika baris error.
- Relasi siswa–wali (`student_guardian`) ganda tidak boleh duplikat.

## Penugasan Guru
- Assign guru ke kelas–mapel (class_subjects); kombinasi unik; bisa edit teacher_id tanpa duplikasi.

## Absensi
- Buat sheet absensi per kelas/mapel/tanggal; status default Hadir.
- Input massal & per siswa; tidak boleh duplikat student per sheet.
- Rekap harian/mingguan/bulanan; filter kelas/mapel/tanggal; export PDF/Excel.
- Lock sheet (locked_at) mencegah edit; user tanpa role yang tepat ditolak.

## Penilaian
- Definisi assessment per kelas-mapel: type (tugas/UH/UTS/UAS), bobot, max score.
- Validasi total bobot per mapel = 100; score 0–100.
- Input nilai per siswa; tidak ada duplikasi assessment+student.
- Rekap nilai berbobot dan rata-rata kelas benar.

## Raport Digital
- Generate draft raport untuk tiap siswa/semester; unique student+semester.
- Approval oleh wali kelas mengubah status ke `approved`; publish mengisi `published_at` dan menyimpan `pdf_path`.
- PDF berisi nilai per mapel, absensi ringkas, dan identitas siswa.
- Hanya wali kelas/role terkait yang bisa approve/publish; orang tua hanya bisa lihat anaknya.

## Portal Orang Tua
- Orang tua melihat absensi, nilai, raport PDF anak yang terkait; akses anak lain ditolak.
- Pengumuman terlihat sesuai target_scope (all/class/parents).

## Dashboard
- Statistik siswa/guru sesuai data master.
- Kehadiran hari ini sesuai data absensi; grafik tren absensi/nilai sesuai filter.

## Keamanan & Kualitas
- Validasi input (XSS/CSRF via middleware bawaan), batas ukuran file import, password hashing.
- Audit log opsional: aksi CRUD absensi/nilai/raport tercatat bila fitur diaktifkan.
- Feature tests prioritas: auth/RBAC, import siswa, absensi sheet+record, penilaian bobot, approval+publish raport, akses orang tua.
