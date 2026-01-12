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
<body class="antialiased">
    <div class="min-h-screen flex items-center justify-center px-4 py-10 bg-slate-950 text-slate-100" style="background: radial-gradient(circle at 20% 20%, rgba(59,130,246,.18), transparent 35%), radial-gradient(circle at 80% 0%, rgba(236,72,153,.15), transparent 30%), #0f172a;">
        <div class="w-full max-w-6xl bg-slate-900/70 border border-slate-800 rounded-2xl shadow-2xl p-6 sm:p-8 space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <span class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-800/70 px-3 py-2 text-sm text-indigo-200">
                    Sistem Informasi Operasional SMP
                </span>
                <div class="flex gap-2 flex-wrap">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition">Dashboard</a>
                        @if(auth()->user()->role?->name === 'Admin TU')
                            <a href="{{ route('admin.panel') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-100 hover:border-indigo-500 transition">Admin Panel</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition">Masuk</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-100 hover:border-indigo-500 transition">Daftar</a>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2 items-center">
                <div class="space-y-4">
                    <h1 class="text-3xl sm:text-4xl font-bold text-white leading-tight">
                        Pantau akademik, absensi, dan raport dalam satu tempat
                    </h1>
                    <p class="text-slate-300">
                        Kelola data siswa, guru, kelas, absensi harian, nilai berbobot, hingga publikasi raport digital. Dirancang ringan untuk PHP 8.x + MySQL.
                    </p>
                    <div class="grid gap-3 sm:grid-cols-3">
                        <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-4">
                            <p class="text-sm text-slate-400">Data Akademik</p>
                            <p class="mt-2 text-lg font-semibold text-slate-100">Tahun Ajaran, Semester, Kelas, Mapel</p>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-4">
                            <p class="text-sm text-slate-400">Absensi & Nilai</p>
                            <p class="mt-2 text-lg font-semibold text-slate-100">Sheet harian, bobot Tugas/UH/UTS/UAS</p>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-4">
                            <p class="text-sm text-slate-400">Raport</p>
                            <p class="mt-2 text-lg font-semibold text-slate-100">Approval wali kelas & PDF</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-400">Akses Cepat</p>
                            <p class="text-lg font-semibold text-white">Role Admin TU</p>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-emerald-500/15 text-emerald-300 text-xs font-semibold">Aktif</span>
                    </div>
                    <div class="space-y-2 text-sm text-slate-200">
                        <div class="flex justify-between"><span>Tahun ajaran & semester aktif</span><span>✔</span></div>
                        <div class="flex justify-between"><span>Penugasan guru ke kelas-mapel</span><span>✔</span></div>
                        <div class="flex justify-between"><span>Absensi & penilaian berbobot</span><span>✔</span></div>
                        <div class="flex justify-between"><span>Raport draft → publish PDF</span><span>✔</span></div>
                    </div>
                    <div class="flex gap-2 flex-wrap">
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-100 hover:border-indigo-500 transition">Masuk</a>
                        @auth
                            <a href="{{ route('admin.panel') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition">Admin Panel</a>
                        @endauth
                    </div>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-4">
                    <p class="text-sm text-slate-400">Absensi</p>
                    <p class="mt-2 text-base font-semibold text-slate-100">Sheet harian per kelas/mapel, lock, ekspor (UI lanjutan menyusul)</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-4">
                    <p class="text-sm text-slate-400">Penilaian</p>
                    <p class="mt-2 text-base font-semibold text-slate-100">Bobot Tugas/UH/UTS/UAS, input nilai, rekap (UI lanjutan menyusul)</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-4">
                    <p class="text-sm text-slate-400">Portal Orang Tua</p>
                    <p class="mt-2 text-base font-semibold text-slate-100">Akses absensi, nilai, raport (akan dibangun)</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
