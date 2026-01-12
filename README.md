# Product Requirements Document (PRD)
## Sistem Informasi Operasional SMP Terpadu

---

## 1. Latar Belakang
Sebagian besar Sekolah Menengah Pertama (SMP) masih mengelola data akademik, absensi, dan laporan secara manual atau semi-digital (Excel, buku absen). Hal ini menyebabkan:
- Data tidak terpusat
- Proses rekap memakan waktu
- Minim transparansi ke orang tua
- Risiko kehilangan dan inkonsistensi data

Sistem ini dirancang untuk membantu SMP mengelola operasional akademik secara terintegrasi, ringan, dan mudah dipelihara oleh SDM lokal.

---

## 2. Tujuan Produk
- Digitalisasi proses akademik SMP
- Memudahkan guru dan TU dalam input & rekap data
- Memberikan transparansi ke orang tua
- Menyediakan laporan cepat untuk kepala sekolah
- Sistem mudah dipelihara & diwariskan ke developer lain

---

## 3. Target Pengguna
1. Admin Tata Usaha
2. Guru Mata Pelajaran
3. Wali Kelas
4. Kepala Sekolah
5. Orang Tua Siswa

---

## 4. Ruang Lingkup Sistem
### In-Scope
- Manajemen data akademik
- Absensi siswa
- Penilaian & raport
- Portal orang tua
- Dashboard monitoring

### Out-of-Scope (versi awal)
- Mobile application native
- Integrasi Dapodik
- E-learning penuh (LMS kompleks)

---

## 5. Role & Hak Akses (RBAC)

### 5.1 Admin TU
- Kelola data siswa, guru, kelas
- Kelola tahun ajaran & semester
- Kelola user & role
- Generate laporan umum

### 5.2 Guru
- Melakukan absensi mapel
- Input nilai siswa
- Melihat rekap kelas yang diajar

### 5.3 Wali Kelas
- Melihat rekap absensi kelas
- Catatan siswa
- Validasi raport

### 5.4 Kepala Sekolah
- Akses dashboard statistik
- Laporan kehadiran & nilai
- Monitoring keseluruhan sekolah

### 5.5 Orang Tua
- Melihat absensi anak
- Melihat nilai & raport
- Menerima pengumuman

---

## 6. Fitur Utama

### 6.1 Manajemen Data Akademik
- Tahun ajaran & semester
- Kelas & jurusan
- Mata pelajaran
- Guru & wali kelas
- Data siswa & orang tua

---

### 6.2 Sistem Absensi
- Absensi harian per kelas
- Status: Hadir, Izin, Sakit, Alfa
- Input cepat oleh guru/wali kelas
- Rekap harian, mingguan, bulanan
- Export PDF & Excel
- Notifikasi (opsional)

---

### 6.3 Sistem Penilaian
- Komponen nilai:
  - Tugas
  - Ulangan Harian
  - UTS
  - UAS
- Bobot nilai dapat diatur
- Input nilai per mapel
- Rekap nilai otomatis

---

### 6.4 Raport Digital
- Generate raport PDF per siswa
- Template sesuai sekolah
- Arsip raport per semester
- Riwayat raport siswa

---

### 6.5 Portal Orang Tua
- Login khusus orang tua
- Lihat absensi anak
- Lihat nilai & raport
- Pengumuman sekolah

---

### 6.6 Dashboard Monitoring
- Total siswa & guru
- Kehadiran hari ini
- Grafik kehadiran per kelas
- Rata-rata nilai per mapel
- Siswa dengan absensi bermasalah

---

## 7. Fitur Opsional (Upsell)
- Absensi QR Code
- Notifikasi WhatsApp Gateway
- Sistem SPP sederhana
- Pengajuan izin online
- Multi tahun ajaran
- Audit log aktivitas user

---

## 8. Non-Functional Requirements

### 8.1 Performance
- Sistem responsif (<2 detik untuk operasi umum)
- Mampu menangani 1.000–2.000 siswa

### 8.2 Security
- Authentication (session/JWT)
- Password hashing (bcrypt)
- Role-based access control
- Validasi input & CSRF protection

### 8.3 Maintainability
- Struktur MVC Laravel standar
- Dokumentasi instalasi
- ERD database tersedia
- Kode mudah dipahami developer PHP umum

---

## 9. Teknologi yang Digunakan

### Backend
- Laravel (PHP)
- MySQL

### Frontend
- Blade Template
- Bootstrap / Tailwind CSS

### Infrastruktur
- VPS / Shared Hosting
- Apache/Nginx
- PHP 8.x
- MySQL 8.x

---

## 10. Estimasi Pengembangan (MVP)

| Modul                     | Estimasi |
|---------------------------|----------|
| Auth & RBAC               | 2 hari   |
| Data Akademik             | 3 hari   |
| Absensi                   | 3 hari   |
| Penilaian                 | 3 hari   |
| Raport PDF                | 2 hari   |
| Dashboard                 | 2 hari   |
| Testing & Deployment      | 2 hari   |
| **Total**                 | **17 hari** |

---

## 11. Deliverables
- Source code
- Database schema
- Dokumentasi instalasi
- Akun admin default
- Manual penggunaan singkat

---

## 12. Risiko & Mitigasi
- **SDM IT sekolah terbatas** → sistem dibuat sederhana & dokumentasi lengkap
- **Perubahan kebijakan sekolah** → sistem modular & configurable
- **Keterbatasan internet** → UI ringan & minim request

---

## 13. Kesimpulan
Sistem Informasi Operasional SMP ini dirancang sebagai solusi praktis, ringan, dan mudah dipelihara untuk mendukung kegiatan akademik sekolah serta meningkatkan transparansi ke orang tua.
