<x-app-layout>


    <div x-data="reportCardsPage({
        reports: @json($reports),
        classRecap: @json($classRecap),
    })">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <template x-for="card in statusCards" :key="card.label">
                    <div class="rounded-xl bg-white border border-gray-100 shadow-sm p-5 flex flex-col gap-2">
                        <div class="text-xs uppercase font-semibold text-gray-500" x-text="card.label"></div>
                        <div class="text-3xl font-bold text-gray-900" x-text="card.value"></div>
                        <p class="text-xs text-gray-500" x-text="card.desc"></p>
                    </div>
                </template>
            </div>

            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Status Raport per Kelas</h3>
                        <p class="text-sm text-gray-500">Mock data. Alur approve/publish akan memakai ReportCardController.</p>
                    </div>
                    <div class="flex gap-2">
                        <select class="rounded-md border-gray-300 text-sm">
                            <option>Semester Ganjil 2025/2026</option>
                            <option>Semester Genap 2025/2026</option>
                        </select>
                        <button class="px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Generate Raport</button>
                    </div>
                </div>
                <x-table :headers="['Kelas', 'Wali Kelas', 'Draft', 'Approved', 'Published', 'Rata-rata Kelas', 'Aksi']">
                    <template x-for="row in classRecap" :key="row.class">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900" x-text="row.class"></td>
                            <td class="px-6 py-4 text-sm text-gray-700" x-text="row.homeroom"></td>
                            <td class="px-6 py-4 text-sm text-gray-700" x-text="row.draft"></td>
                            <td class="px-6 py-4 text-sm text-gray-700" x-text="row.approved"></td>
                            <td class="px-6 py-4 text-sm text-gray-700" x-text="row.published"></td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="flex items-center gap-2">
                                    <div class="h-1.5 w-20 rounded-full bg-gray-100 overflow-hidden">
                                        <div class="h-full bg-indigo-500" :style="`width: ${row.avg}%`"></div>
                                    </div>
                                    <span x-text="row.avg"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-indigo-600">
                                <button class="hover:underline">Detail</button>
                            </td>
                        </tr>
                    </template>
                </x-table>
            </x-card>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <x-card>
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Preview Raport</h3>
                            <p class="text-sm text-gray-500">Layout dummy untuk PDF raport.</p>
                        </div>
                        <button class="px-3 py-2 bg-gray-100 text-gray-800 rounded-lg text-sm hover:bg-gray-200">Unduh PDF</button>
                    </div>
                    <div class="border border-dashed border-gray-200 rounded-lg p-4 bg-white">
                        <div class="flex items-center justify-between mb-2 text-sm text-gray-700">
                            <span>Nama: <strong>Darma Saputra</strong></span>
                            <span>Kelas: VII-A</span>
                        </div>
                        <div class="text-xs text-gray-500 mb-4">Semester Ganjil 2025/2026</div>
                        <div class="overflow-hidden rounded-md border border-gray-100">
                            <table class="min-w-full divide-y divide-gray-100">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Mapel</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Nilai Akhir</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Predikat</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white text-sm text-gray-900">
                                    <tr>
                                        <td class="px-3 py-2">Matematika</td>
                                        <td class="px-3 py-2">88</td>
                                        <td class="px-3 py-2">B+</td>
                                        <td class="px-3 py-2">Baik, pertahankan</td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2">IPA</td>
                                        <td class="px-3 py-2">84</td>
                                        <td class="px-3 py-2">B</td>
                                        <td class="px-3 py-2">Perlu latihan eksperimen</td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2">Bahasa Indonesia</td>
                                        <td class="px-3 py-2">90</td>
                                        <td class="px-3 py-2">A-</td>
                                        <td class="px-3 py-2">Membaca lancar</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 text-sm text-gray-700">
                            <p><strong>Catatan Wali Kelas:</strong> Siswa aktif, teruskan disiplin.</p>
                            <p class="mt-2"><strong>Status:</strong> Approved (menunggu publish).</p>
                        </div>
                    </div>
                </x-card>

                <x-card>
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Timeline Approval</h3>
                            <p class="text-sm text-gray-500">Ilustrasi langkah: draft → approve → publish.</p>
                        </div>
                        <button class="px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Setujui & Publish</button>
                    </div>
                    <ol class="space-y-4">
                        <li class="flex items-start gap-3">
                            <div class="h-10 w-10 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold">1</div>
                            <div>
                                <p class="font-semibold text-gray-900">Draft</p>
                                <p class="text-sm text-gray-600">Nilai per mapel diisi otomatis dari rekap assessment berbobot.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="h-10 w-10 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold">2</div>
                            <div>
                                <p class="font-semibold text-gray-900">Approval Wali Kelas</p>
                                <p class="text-sm text-gray-600">Wali kelas meninjau absensi + nilai, menambahkan catatan.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="h-10 w-10 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold">3</div>
                            <div>
                                <p class="font-semibold text-gray-900">Publish</p>
                                <p class="text-sm text-gray-600">PDF tersimpan di storage, orang tua dapat mengunduh.</p>
                            </div>
                        </li>
                    </ol>
                </x-card>
            </div>
        </div>
    </div>

    <script>
        function reportCardsPage(initial) {
            return {
                statusCards: (initial.reports || []).length
                    ? [
                        { label: 'Draft', value: initial.reports.filter(r => r.status === 'draft').length, desc: 'Butuh approval wali kelas' },
                        { label: 'Approved', value: initial.reports.filter(r => r.status === 'approved').length, desc: 'Siap publish' },
                        { label: 'Published', value: initial.reports.filter(r => r.status === 'published').length, desc: 'Sudah terbit' },
                    ]
                    : [
                        { label: 'Draft', value: 0, desc: 'Belum ada data' },
                        { label: 'Approved', value: 0, desc: 'Belum ada data' },
                        { label: 'Published', value: 0, desc: 'Belum ada data' },
                    ],
                reports: initial.reports || [],
                classRecap: initial.classRecap || [],
            };
        }
    </script>
</x-app-layout>
