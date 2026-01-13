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
<body class="antialiased bg-white text-gray-900 font-sans">
    
    <!-- Navbar -->
    <header class="fixed w-full top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo / Brand -->
                <div class="flex-shrink-0 flex items-center gap-2">
                    <div class="bg-indigo-600 text-white p-1.5 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="font-bold text-xl tracking-tight text-gray-900">Sistem Informasi SMP</span>
                </div>

                <!-- Navigation -->
                <nav class="hidden md:flex gap-8">
                    <!-- Add public links here if needed -->
                </nav>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            @php $roleName = Auth::user()?->role?->name; @endphp
                            @if($roleName === 'Admin TU')
                                <a href="{{ route('admin.panel') }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600 transition">Dashboard</a>
                            @elseif($roleName === 'Guru')
                                <a href="{{ route('guru.dashboard') }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600 transition">Dashboard</a>
                            @elseif($roleName === 'Wali Kelas')
                                <a href="{{ route('wali.dashboard') }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600 transition">Dashboard</a>
                            @elseif($roleName === 'Kepala Sekolah')
                                <a href="{{ route('kepsek.dashboard') }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600 transition">Dashboard</a>
                            @elseif($roleName === 'Orang Tua')
                                <a href="{{ route('parent.dashboard') }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600 transition">Dashboard</a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600 transition">Masuk</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-full shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                    Daftar
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <div class="relative pt-32 pb-20 sm:pt-40 sm:pb-24 overflow-hidden">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
            <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                <span class="block">Transformasi Digital</span>
                <span class="block text-indigo-600">Manajemen Sekolah</span>
            </h1>
            <p class="mt-4 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                Platform terintegrasi untuk mengelola data akademik, kesiswaan, dan operasional sekolah dengan efisien, akurat, dan transparan.
            </p>
            <div class="mt-8 max-w-md mx-auto sm:flex sm:justify-center md:mt-10">
                <div class="rounded-md shadow">
                    @auth
                        @php $roleName = Auth::user()?->role?->name; @endphp
                        @if($roleName === 'Admin TU')
                            <a href="{{ route('admin.panel') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10 transition">
                                Ke Dashboard
                            </a>
                        @elseif($roleName === 'Guru')
                            <a href="{{ route('guru.dashboard') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10 transition">
                                Ke Dashboard
                            </a>
                        @elseif($roleName === 'Wali Kelas')
                            <a href="{{ route('wali.dashboard') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10 transition">
                                Ke Dashboard
                            </a>
                        @elseif($roleName === 'Kepala Sekolah')
                            <a href="{{ route('kepsek.dashboard') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10 transition">
                                Ke Dashboard
                            </a>
                        @elseif($roleName === 'Orang Tua')
                            <a href="{{ route('parent.dashboard') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10 transition">
                                Ke Dashboard
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10 transition">
                            Mulai Sekarang
                        </a>
                    @endauth
                </div>
            </div>
        </div>
        
        <!-- Background Decoration -->
        <div class="absolute top-0 inset-x-0 h-full -z-10 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-indigo-50/50 to-white"></div>
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-indigo-100 rounded-full blur-3xl opacity-50"></div>
            <div class="absolute bottom-0 -left-24 w-72 h-72 bg-blue-50 rounded-full blur-3xl opacity-50"></div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-16 bg-white border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Fitur Unggulan</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Solusi Lengkap Akademik
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Feature 1 -->
                <div class="relative p-6 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center mb-4 text-indigo-600 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Manajemen Data</h3>
                    <p class="mt-2 text-base text-gray-500">
                        Pengelolaan data siswa, guru, dan staf yang terpusat dan mudah diakses kapan saja.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="relative p-6 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center mb-4 text-indigo-600 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Akademik & Penilaian</h3>
                    <p class="mt-2 text-base text-gray-500">
                        Sistem pencatatan nilai, absensi, dan jadwal pelajaran yang terintegrasi secara digital.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="relative p-6 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center mb-4 text-indigo-600 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Laporan Real-time</h3>
                    <p class="mt-2 text-base text-gray-500">
                        Pemantauan progress dan pelaporan hasil belajar siswa yang dapat diakses secara real-time.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-50 border-t border-gray-200">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center bg-gray-50">
            <div class="text-gray-400 text-sm">
                &copy; {{ date('Y') }} Sistem Informasi SMP. All rights reserved.
            </div>
            <div class="flex gap-6 mt-4 md:mt-0">
                <a href="#" class="text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Kebijakan Privasi</span>
                    Privasi
                </a>
                <a href="#" class="text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Syarat & Ketentuan</span>
                    Syarat
                </a>
            </div>
        </div>
    </footer>

</body>
</html>
