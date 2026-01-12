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
<body class="antialiased bg-white text-gray-900">
    <header class="border-b border-gray-200 bg-gradient-to-r from-indigo-50 via-white to-emerald-50">
        <div class="max-w-6xl mx-auto px-4 py-10 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-2">
                <p class="text-sm font-semibold text-indigo-700">Sistem Informasi Operasional SMP</p>
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 leading-tight">Kelola akademik, absensi, dan raport dengan rapi</h1>
                <p class="text-gray-600 max-w-3xl">Fokus ke data inti: siswa, guru, kelas, absensi harian, penilaian berbobot, dan publikasi raport digital.</p>
            </div>
            <div class="flex flex-wrap gap-2">
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
    </header>

    <main class="max-w-6xl mx-auto px-4 py-10 space-y-10">
        <section class="grid gap-6 sm:grid-cols-3">
            <div class="space-y-1">
                <p class="text-sm text-gray-500">Data Master</p>
                <p class="text-lg font-semibold text-gray-900">Tahun ajaran, semester, kelas, mapel, guru, siswa.</p>
            </div>
            <div class="space-y-1">
                <p class="text-sm text-gray-500">Absensi & Nilai</p>
                <p class="text-lg font-semibold text-gray-900">Sheet harian, status hadir/izin/sakit/alfa, bobot Tugas/UH/UTS/UAS.</p>
            </div>
            <div class="space-y-1">
                <p class="text-sm text-gray-500">Raport Digital</p>
                <p class="text-lg font-semibold text-gray-900">Approval wali kelas, publish PDF, arsip per semester.</p>
            </div>
        </section>

        <section class="grid gap-6 sm:grid-cols-2">
            <div class="p-1 sm:p-2">
                <h2 class="text-xl font-semibold text-gray-900">Akses Cepat Admin TU</h2>
                <ul class="mt-3 space-y-2 text-sm text-gray-700">
                    <li>• Set tahun ajaran & semester aktif</li>
                    <li>• Penugasan guru ke kelas-mapel</li>
                    <li>• Absensi & penilaian berbobot</li>
                    <li>• Raport draft → publish PDF</li>
                </ul>
            </div>
            <div class="p-1 sm:p-2">
                <h2 class="text-xl font-semibold text-gray-900">Langkah Berikutnya</h2>
                <ul class="mt-3 space-y-2 text-sm text-gray-700">
                    <li>• Portal Orang Tua: akses absensi, nilai, raport.</li>
                    <li>• Dashboard eksekutif: kehadiran & rekap nilai.</li>
                    <li>• Import/Export Excel, template PDF raport.</li>
                </ul>
            </div>
        </section>
    </main>
</body>
</html>
