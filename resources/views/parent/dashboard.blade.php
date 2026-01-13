<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            Portal Orang Tua
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8" x-data="parentDashboard()">
            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                <h2 class="text-2xl font-bold">Selamat Datang, Bapak/Ibu</h2>
                <p class="mt-1 text-blue-100">Pantau perkembangan belajar putra-putri Anda secara real-time</p>
            </div>

            <!-- Children Cards -->
            <template x-if="loading">
                <div class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <p class="mt-2 text-gray-600">Memuat data...</p>
                </div>
            </template>

            <template x-if="error">
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <span x-text="error"></span>
                </div>
            </template>

            <div x-show="!loading && !error" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="student in students" :key="student.id">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
                        <!-- Student Header -->
                        <div class="bg-blue-50 px-6 py-4 border-b border-blue-100">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    <span x-text="student.full_name.charAt(0)"></span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900" x-text="student.full_name"></h3>
                                    <p class="text-sm text-gray-600" x-text="`NIS: ${student.nis}`"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Student Info -->
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Kelas</span>
                                <span class="font-semibold text-gray-900" x-text="student.class || '-'"></span>
                            </div>

                            <!-- Attendance Summary -->
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Kehadiran Bulan Ini</h4>
                                <div class="grid grid-cols-4 gap-2 text-center text-xs">
                                    <template x-for="[key, label] in [['hadir', 'H'], ['izin', 'I'], ['sakit', 'S'], ['alfa', 'A']]">
                                        <div>
                                            <div class="font-bold text-lg" x-text="student.attendance_summary[key] || 0"></div>
                                            <div class="text-gray-500" x-text="label"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Latest Report Cards -->
                            <div x-show="student.latest_report_cards && student.latest_report_cards.length > 0">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Raport Terakhir</h4>
                                <template x-for="report in student.latest_report_cards" :key="report.id">
                                    <div class="flex justify-between items-center text-sm py-1">
                                        <span x-text="report.semester"></span>
                                        <span class="font-semibold" x-text="report.average_score ? report.average_score.toFixed(1) : '-'"></span>
                                    </div>
                                </template>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2 pt-2">
                                <a :href="`/parent/students/${student.id}/attendance`"
                                   class="flex-1 text-center px-3 py-2 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100 transition">
                                    Absensi
                                </a>
                                <a :href="`/parent/students/${student.id}/report-cards`"
                                   class="flex-1 text-center px-3 py-2 bg-green-50 text-green-600 rounded-lg text-sm font-medium hover:bg-green-100 transition">
                                    Nilai & Raport
                                </a>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Announcements Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Pengumuman Sekolah</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <template x-if="announcements.length === 0">
                        <div class="px-6 py-8 text-center text-gray-500">
                            <p>Tidak ada pengumuman saat ini</p>
                        </div>
                    </template>
                    <template x-for="announcement in announcements" :key="announcement.id">
                        <div class="px-6 py-4 hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900" x-text="announcement.title"></h4>
                                    <p class="text-sm text-gray-600 mt-1 line-clamp-2" x-text="announcement.body?.substring(0, 150) + '...'"></p>
                                    <p class="text-xs text-gray-500 mt-2" x-text="formatDate(announcement.published_at)"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
        function parentDashboard() {
            return {
                loading: true,
                error: null,
                students: [],
                announcements: [],

                async init() {
                    try {
                        const response = await fetch('/parent/dashboard', {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                        });

                        if (!response.ok) {
                            throw new Error('Gagal memuat data');
                        }

                        const data = await response.json();
                        this.students = data.students || [];
                        this.announcements = data.announcements || [];
                    } catch (e) {
                        this.error = e.message;
                    } finally {
                        this.loading = false;
                    }
                },

                formatDate(dateString) {
                    if (!dateString) return '-';
                    const date = new Date(dateString);
                    return new Intl.DateTimeFormat('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric',
                    }).format(date);
                },
            };
        }
    </script>
</x-app-layout>