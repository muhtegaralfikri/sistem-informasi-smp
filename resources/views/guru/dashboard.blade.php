<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Welcome -->
        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Selamat Datang, {{ $teacher?->full_name ?? Auth::user()->name }}</h2>
                    <p class="text-sm text-gray-500">Dashboard Guru</p>
                </div>
            </div>
        </x-card>

        <!-- Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-card>
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-blue-100">
                        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $classSubjects->count() }}</p>
                        <p class="text-sm text-gray-500">Kelas-Mapel Diampu</p>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-green-100">
                        <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $attendanceCount }}</p>
                        <p class="text-sm text-gray-500">Sesi Absensi Dibuat</p>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-amber-100">
                        <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $todaySchedules->count() }}</p>
                        <p class="text-sm text-gray-500">Jadwal Hari Ini</p>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Today's Schedule -->
        <x-card>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Jadwal Mengajar Hari Ini</h3>
            @if($todaySchedules->isEmpty())
                <div class="text-center py-6 text-gray-400 italic">
                    Tidak ada jadwal mengajar hari ini.
                </div>
            @else
                <div class="divide-y">
                    @foreach($todaySchedules as $schedule)
                        <div class="py-3 flex justify-between items-center">
                            <div>
                                <p class="font-medium text-gray-900">{{ $schedule->classSubject->subject->name }}</p>
                                <p class="text-sm text-gray-500">{{ $schedule->classSubject->classRoom->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-mono text-sm text-indigo-600">
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>

        <!-- Class Subjects List -->
        <x-card>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Kelas & Mapel yang Diampu</h3>
            @if($classSubjects->isEmpty())
                <div class="text-center py-6 text-gray-400 italic">
                    Anda belum ditugaskan ke kelas/mapel manapun.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($classSubjects as $cs)
                        <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                            <p class="font-medium text-gray-900">{{ $cs->subject->name }}</p>
                            <p class="text-sm text-gray-500">{{ $cs->classRoom->name }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>
    </div>
</x-app-layout>
