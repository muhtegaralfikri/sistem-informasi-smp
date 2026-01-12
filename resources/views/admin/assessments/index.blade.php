<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Penilaian & Rekap Nilai') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="assessmentsPage({
        weightStats: @json($weightStats),
        assessments: @json($assessments),
        gradeInputs: @json($gradeInputs),
    })">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <template x-for="stat in weightStats" :key="stat.mapel">
                    <div class="rounded-xl bg-white border border-gray-100 shadow-sm p-5 flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs uppercase font-semibold text-gray-500">Bobot</div>
                                <div class="text-lg font-bold text-gray-900" x-text="stat.mapel"></div>
                            </div>
                            <span class="text-sm font-semibold px-2 py-1 rounded-md" :class="stat.total === 100 ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700'">
                                <span x-text="stat.total + '%'"></span>
                            </span>
                        </div>
                        <div class="space-y-2">
                            <template x-for="item in stat.items" :key="item.type">
                                <div class="flex items-center justify-between text-sm text-gray-700">
                                    <span class="capitalize" x-text="item.type"></span>
                                    <span x-text="item.weight + '%'"></span>
                                </div>
                            </template>
                        </div>
                        <p class="text-xs text-gray-500">Validasi backend perlu memastikan total = 100%.</p>
                    </div>
                </template>
            </div>

            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Penilaian</h3>
                        <p class="text-sm text-gray-500">Mock data 8 assessment terbaru. Integrasi dengan /admin/assessments API menyusul.</p>
                    </div>
                    <button class="px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">+ Tambah Penilaian</button>
                </div>
                <x-table :headers="['Kelas / Mapel', 'Judul', 'Tipe', 'Bobot', 'Max', 'Jatuh Tempo', 'Status Nilai']">
                    <template x-for="item in assessments" :key="item.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-semibold" x-text="item.class"></div>
                                <div class="text-xs text-gray-500" x-text="item.subject"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900" x-text="item.title"></td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700" x-text="item.type.toUpperCase()"></span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900" x-text="item.weight + '%'"></td>
                            <td class="px-6 py-4 text-sm text-gray-900" x-text="item.max_score"></td>
                            <td class="px-6 py-4 text-sm text-gray-700" x-text="item.due_date"></td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div class="flex items-center gap-2">
                                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-full bg-green-500" :style="`width: ${item.progress}%`"></div>
                                    </div>
                                    <span class="text-xs text-gray-600" x-text="item.progress + '%'"></span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1" x-text="item.status"></div>
                            </td>
                        </tr>
                    </template>
                </x-table>
            </x-card>

            <x-card>
                <div class="flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Input Nilai (contoh mock)</h3>
                            <p class="text-sm text-gray-500">Table ini hanya ilustrasi UI. API gradesUpsert akan dipakai saat backend siap.</p>
                        </div>
                        <div class="flex gap-2">
                            <select class="rounded-md border-gray-300 text-sm">
                                <option>VII-A - Matematika</option>
                                <option>VIII-B - IPA</option>
                            </select>
                            <select class="rounded-md border-gray-300 text-sm">
                                <option>UTS</option>
                                <option>UAS</option>
                                <option>UH</option>
                                <option>Tugas</option>
                            </select>
                            <button class="px-3 py-2 bg-gray-100 text-gray-800 rounded-lg text-sm hover:bg-gray-200">Pilih</button>
                        </div>
                    </div>
                    <div class="overflow-hidden rounded-lg border border-gray-100">
                        <div class="bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800">VII-A - Matematika - UTS</div>
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-white">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama Siswa</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">NIS</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nilai</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <template x-for="row in gradeInputs" :key="row.id">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900" x-text="row.name"></td>
                                        <td class="px-4 py-3 text-sm text-gray-600" x-text="row.nis"></td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <input type="number" min="0" max="100" class="w-24 rounded-md border-gray-200" :value="row.score">
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            <input type="text" class="w-full rounded-md border-gray-200" placeholder="Catatan (opsional)" :value="row.note">
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between">
                            <span class="text-xs text-gray-500">Nilai disimpan saat backend siap.</span>
                            <div class="flex gap-2">
                                <button class="px-3 py-2 text-sm bg-gray-100 rounded-lg text-gray-700 hover:bg-gray-200">Simpan Draft</button>
                                <button class="px-3 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Publikasikan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <script>
        function assessmentsPage(initial) {
            return {
                weightStats: initial.weightStats || [],
                assessments: initial.assessments || [],
                gradeInputs: initial.gradeInputs || [],
            };
        }
    </script>
</x-app-layout>
