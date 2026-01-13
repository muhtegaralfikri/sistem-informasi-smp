<x-app-layout>


    <div x-data="parentPortalPage({
        student: @json($student),
        reports: @json($reports),
        announcements: @json($announcements),
    })">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <x-card>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Ringkasan Anak</h3>
                        <p class="text-sm text-gray-500">Mock UI yang akan dilihat orang tua. Data masih statis.</p>
                    </div>
                    <select class="rounded-md border-gray-300 text-sm">
                        <option>Darma Saputra</option>
                        <option>Indah Wijaya</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <template x-for="card in overview" :key="card.label">
                        <div class="rounded-xl bg-white border border-gray-100 shadow-sm p-5">
                            <div class="text-xs uppercase font-semibold text-gray-500" x-text="card.label"></div>
                            <div class="mt-2 text-3xl font-bold text-gray-900" x-text="card.value"></div>
                            <p class="text-xs text-gray-500" x-text="card.desc"></p>
                        </div>
                    </template>
                </div>
            </x-card>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Absensi Mingguan</h3>
                    <div class="space-y-3">
                        <template x-for="rec in attendance" :key="rec.date">
                            <div class="flex items-center justify-between rounded-lg border border-gray-100 px-3 py-2">
                                <div>
                                    <div class="text-sm font-medium text-gray-900" x-text="rec.date"></div>
                                    <div class="text-xs text-gray-500" x-text="rec.subject"></div>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-md"
                                      :class="rec.status === 'Hadir' ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700'">
                                    <span x-text="rec.status"></span>
                                </span>
                            </div>
                        </template>
                    </div>
                </x-card>

                <x-card class="lg:col-span-2">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Nilai & Rekap</h3>
                            <p class="text-sm text-gray-500">Nilai akhir per mapel (contoh).</p>
                        </div>
                        <button class="px-3 py-2 bg-gray-100 text-gray-800 rounded-lg text-sm hover:bg-gray-200">Lihat Detail</button>
                    </div>
                    <div class="overflow-hidden rounded-lg border border-gray-100">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Mapel</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nilai</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Predikat</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Catatan Guru</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <template x-for="row in grades" :key="row.subject">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900" x-text="row.subject"></td>
                                        <td class="px-4 py-3 text-sm text-gray-900" x-text="row.score"></td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="px-2 py-1 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700" x-text="row.predicate"></span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700" x-text="row.note"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>

            <x-card>
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Raport</h3>
                        <p class="text-sm text-gray-500">Tautan PDF raport terbaru untuk orang tua.</p>
                    </div>
                    <button class="px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Unduh PDF</button>
                </div>
                <div class="rounded-lg border border-dashed border-gray-200 p-4 bg-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-semibold text-gray-900">Raport Semester Ganjil 2025/2026</div>
                            <div class="text-xs text-gray-500">Terbit: 20 Februari 2026</div>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-md bg-green-50 text-green-700">Published</span>
                    </div>
                    <p class="mt-2 text-sm text-gray-700">Ringkasan: Rata-rata 84, kehadiran 92%, catatan wali kelas: “Siswa aktif dan disiplin”.</p>
                </div>
            </x-card>
        </div>
    </div>

    <script>
        function parentPortalPage(initial) {
            return {
                overview: [
                    { label: 'Rata-rata Nilai', value: initial.reports?.[0]?.average ?? '-', desc: initial.reports?.[0]?.semester ?? 'Semester belum ada' },
                    { label: 'Kehadiran', value: '—', desc: 'Butuh data absensi' },
                    { label: 'Pengumuman', value: (initial.announcements || []).length.toString(), desc: 'Masuk dari tabel pengumuman' },
                ],
                attendance: [
                    { date: '—', subject: 'Belum ada data absensi', status: 'Hadir' },
                ],
                grades: [
                    { subject: 'Matematika', score: 0, predicate: '-', note: 'Menunggu data nilai' },
                ],
            };
        }
    </script>
</x-app-layout>
