<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Sistem Informasi SMP') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            body { margin: 0; font-family: 'Inter', system-ui, -apple-system, sans-serif; background: #0f172a; color: #e2e8f0; }
            a { color: inherit; text-decoration: none; }
            .hero { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; background: radial-gradient(circle at 20% 20%, rgba(59,130,246,.15), transparent 35%), radial-gradient(circle at 80% 0%, rgba(236,72,153,.12), transparent 30%), #0f172a; }
            .card { max-width: 1200px; width: 100%; background: #0b1224; border: 1px solid #1e293b; border-radius: 16px; padding: 32px; box-shadow: 0 20px 50px rgba(0,0,0,0.35); }
            .btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 16px; border-radius: 10px; font-weight: 600; border: 1px solid transparent; transition: all .2s ease; }
            .btn-primary { background: linear-gradient(120deg, #2563eb, #22c55e); color: #f8fafc; }
            .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(37,99,235,.25); }
            .btn-ghost { border-color: #1e293b; color: #cbd5e1; }
            .grid { display: grid; gap: 16px; }
            .grid-3 { grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-top: 24px; }
            .pill { display: inline-flex; align-items: center; gap: 6px; padding: 8px 12px; border-radius: 999px; background: #111827; color: #a5b4fc; border: 1px solid #1f2937; font-size: 14px; }
            .stat { padding: 16px; border-radius: 12px; background: #0f172a; border: 1px solid #1e293b; }
            .stat .label { color: #94a3b8; font-size: 14px; }
            .stat .value { font-size: 28px; font-weight: 700; color: #e2e8f0; margin-top: 6px; }
            @media (max-width: 640px) { .card { padding: 24px; } }
        </style>
    @endif
</head>
<body class="antialiased">
    <div class="hero">
        <div class="card">
            <div style="display:flex; flex-direction:column; gap:24px;">
                <div style="display:flex; flex-wrap:wrap; gap:12px; align-items:center; justify-content:space-between;">
                    <div class="pill">Sistem Informasi Operasional SMP</div>
                    @auth
                        <div style="display:flex; gap:8px;">
                            <a class="btn btn-primary" href="{{ route('dashboard') }}">Buka Dashboard</a>
                            @if(auth()->user()->role?->name === 'Admin TU')
                                <a class="btn btn-ghost" href="{{ route('admin.panel') }}">Admin Panel</a>
                            @endif
                        </div>
                    @else
                        <div style="display:flex; gap:8px;">
                            <a class="btn btn-primary" href="{{ route('login') }}">Masuk</a>
                            @if (Route::has('register'))
                                <a class="btn btn-ghost" href="{{ route('register') }}">Daftar</a>
                            @endif
                        </div>
                    @endauth
                </div>

                <div style="display:grid; grid-template-columns: repeat(auto-fit,minmax(280px,1fr)); gap:24px; align-items:center;">
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        <h1 style="font-size:32px; font-weight:700; color:#f8fafc;">Pantau akademik, absensi, dan raport dalam satu tempat</h1>
                        <p style="color:#cbd5e1; line-height:1.6;">
                            Kelola data siswa, guru, kelas, absensi harian, nilai, hingga publikasi raport digital. Dirancang ringan, simpel, dan siap jalan di hosting PHP 8.x + MySQL.
                        </p>
                        <div class="grid grid-3">
                            <div class="stat">
                                <p class="label">Data Akademik</p>
                                <p class="value">Tahun Ajaran, Semester, Kelas, Mapel</p>
                            </div>
                            <div class="stat">
                                <p class="label">Absensi & Nilai</p>
                                <p class="value">Sheet harian, bobot tugas/UH/UTS/UAS</p>
                            </div>
                            <div class="stat">
                                <p class="label">Raport</p>
                                <p class="value">Approval wali kelas & PDF</p>
                            </div>
                        </div>
                    </div>
                    <div style="background:#0f172a; border:1px solid #1e293b; border-radius:14px; padding:16px; display:grid; gap:12px;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <p style="color:#94a3b8; font-size:14px;">Akses Cepat</p>
                                <p style="color:#e2e8f0; font-weight:600;">Role Admin TU</p>
                            </div>
                            <span style="padding:6px 12px; border-radius:999px; background:#16a34a20; color:#4ade80; font-weight:600;">Aktif</span>
                        </div>
                        <div style="display:grid; gap:8px; color:#cbd5e1; font-size:14px;">
                            <div style="display:flex; justify-content:space-between;">
                                <span>Tahun ajaran & semester aktif</span>
                                <span>✔</span>
                            </div>
                            <div style="display:flex; justify-content:space-between;">
                                <span>Penugasan guru ke kelas-mapel</span>
                                <span>✔</span>
                            </div>
                            <div style="display:flex; justify-content:space-between;">
                                <span>Absensi & penilaian berbobot</span>
                                <span>✔</span>
                            </div>
                            <div style="display:flex; justify-content:space-between;">
                                <span>Raport draft → publish PDF</span>
                                <span>✔</span>
                            </div>
                        </div>
                        <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:8px;">
                            <a class="btn btn-ghost" href="{{ route('login') }}">Masuk</a>
                            @if(auth()->user())
                                <a class="btn btn-primary" href="{{ route('admin.panel') }}">Admin Panel</a>
                            @endif
                        </div>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: repeat(auto-fit,minmax(240px,1fr)); gap:16px; margin-top:8px;">
                    <div class="stat">
                        <p class="label">Absensi</p>
                        <p class="value" style="font-size:18px;">Sheet harian per kelas/mapel, lock, ekspor (pending UI)</p>
                    </div>
                    <div class="stat">
                        <p class="label">Penilaian</p>
                        <p class="value" style="font-size:18px;">Bobot Tugas/UH/UTS/UAS, input nilai, rekap (pending UI)</p>
                    </div>
                    <div class="stat">
                        <p class="label">Portal Orang Tua</p>
                        <p class="value" style="font-size:18px;">Akses absensi, nilai, raport (akan dibangun)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
