<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Sistem Informasi SMP') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-900">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-5xl bg-white border border-gray-200 rounded-2xl shadow-xl p-6 sm:p-10 space-y-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-indigo-600">Sistem Informasi Operasional SMP</p>
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-1">Satu pintu untuk data akademik sekolah</h1>
                    <p class="text-gray-600 mt-2">Kelola siswa, guru, kelas, absensi, nilai, hingga raport digital dengan alur sederhana.</p>
                </div>
                <div class="flex gap-2 flex-wrap">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition">Dashboard</a>
                        @if(auth()->user()->role?->name === 'Admin TU')
                            <a href="{{ route('admin.panel') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-800 hover:border-indigo-500 transition">Admin Panel</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition">Masuk</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-800 hover:border-indigo-500 transition">Daftar</a>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <p class="text-sm text-gray-500">Data Master</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900">Tahun ajaran, semester, kelas, mapel, guru, siswa.</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <p class="text-sm text-gray-500">Absensi & Nilai</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900">Sheet harian, status hadir/izin/sakit/alfa, bobot tugas/UH/UTS/UAS.</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <p class="text-sm text-gray-500">Raport Digital</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900">Approval wali kelas, publish PDF, arsip per semester.</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-gray-200 bg-white p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Akses Cepat</p>
                            <p class="text-xl font-semibold text-gray-900">Role Admin TU</p>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold">Aktif</span>
                    </div>
                    <ul class="mt-3 space-y-2 text-sm text-gray-700">
                        <li>• Set tahun ajaran & semester aktif</li>
                        <li>• Penugasan guru ke kelas-mapel</li>
                        <li>• Absensi & penilaian berbobot</li>
                        <li>• Raport draft → publish PDF</li>
                    </ul>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-5">
                    <p class="text-sm text-gray-500">Langkah Selanjutnya</p>
                    <div class="mt-3 space-y-2 text-sm text-gray-700">
                        <p>Portal Orang Tua (akses absensi/nilai/raport) dan dashboard eksekutif akan ditambahkan.</p>
                        <p>Import/Export Excel dan template PDF raport disiapkan dalam fase berikutnya.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
