<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Dashboard Overview') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Welcome Card -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
            <div class="p-6 sm:p-8 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl mx-4 mt-4 mb-4 shadow-lg relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="text-2xl font-bold">Halo, {{ auth()->user()->name }}! 👋</h3>
                    <p class="mt-2 text-indigo-100 max-w-xl">Selamat datang di Sistem Informasi SMP. Anda login sebagai <span class="font-semibold text-white bg-indigo-500/30 px-2 py-0.5 rounded">{{ auth()->user()->role?->name ?? 'User' }}</span>.</p>
                </div>
                <!-- Decor -->
                <div class="absolute right-0 top-0 h-full w-1/3 bg-white/10 skew-x-12 transform origin-bottom-right"></div>
            </div>
            
            <div class="p-6 pt-0">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                     <!-- Stat Card 1 -->
                    <div class="bg-gray-50 rounded-lg p-5 border border-gray-100 flex items-center gap-4">
                        <div class="p-3 bg-blue-100 text-blue-600 rounded-full">
                           <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Semester Aktif</p>
                            <p class="text-lg font-bold text-gray-900">Ganjil 2025/2026</p>
                        </div>
                    </div>

                    <!-- Stat Card 2 -->
                    <div class="bg-gray-50 rounded-lg p-5 border border-gray-100 flex items-center gap-4">
                        <div class="p-3 bg-emerald-100 text-emerald-600 rounded-full">
                           <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status Akun</p>
                            <p class="text-lg font-bold text-gray-900">Aktif</p>
                        </div>
                    </div>

                     <!-- Stat Card 3 -->
                    <div class="bg-gray-50 rounded-lg p-5 border border-gray-100 flex items-center gap-4">
                         <div class="p-3 bg-purple-100 text-purple-600 rounded-full">
                           <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Peran</p>
                            <p class="text-lg font-bold text-gray-900">{{ auth()->user()->role?->name ?? 'Pengguna' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions based on Role -->
        @if(auth()->user()->role?->name === 'Admin TU')
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Akses Cepat Admin</h3>
             <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.students.index') }}" class="flex flex-col items-center justify-center p-4 bg-white border border-gray-200 rounded-lg hover:border-indigo-500 hover:shadow-md transition group text-center h-32">
                    <div class="p-2 bg-indigo-50 text-indigo-600 rounded-full mb-2 group-hover:bg-indigo-600 group-hover:text-white transition">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-indigo-600">Kelola Siswa</span>
                </a>

                <a href="#" class="flex flex-col items-center justify-center p-4 bg-white border border-gray-200 rounded-lg hover:border-indigo-500 hover:shadow-md transition group text-center h-32">
                     <div class="p-2 bg-indigo-50 text-indigo-600 rounded-full mb-2 group-hover:bg-indigo-600 group-hover:text-white transition">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-indigo-600">Data Kelas</span>
                </a>

                <a href="#" class="flex flex-col items-center justify-center p-4 bg-white border border-gray-200 rounded-lg hover:border-indigo-500 hover:shadow-md transition group text-center h-32">
                     <div class="p-2 bg-indigo-50 text-indigo-600 rounded-full mb-2 group-hover:bg-indigo-600 group-hover:text-white transition">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-indigo-600">Tahun Ajaran</span>
                </a>
             </div>
        </div>
        @endif
    </div>
</x-app-layout>
