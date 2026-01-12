<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Absensi Kelas & Mapel') }}
        </h2>
    </x-slot>

    @php
        $sheetPayload = $sheets->map(function ($s) {
            return [
                'id' => $s->id,
                'class' => $s->classRoom->name ?? '-',
                'subject' => $s->subject->name ?? '-',
                'teacher' => $s->teacher->full_name ?? '-',
                'date' => optional($s->date)->format('Y-m-d'),
                'session' => $s->session,
                'locked' => !is_null($s->locked_at),
                'present' => 0,
            ];
        });

        $studentPayload = $students->map(function ($st) {
            return [
                'id' => $st->id,
                'name' => $st->full_name,
                'nis' => $st->nis,
                'status' => 'hadir',
                'note' => '',
            ];
        });
    @endphp

    <div class="py-12" x-data="attendancePage({
        classes: @json($classes->map(fn($c) => ['id' => $c->id, 'name' => $c->name])),
        subjects: @json($subjects->map(fn($s) => ['id' => $s->id, 'name' => $s->name])),
        teachers: @json($teachers->map(fn($t) => ['id' => $t->id, 'name' => $t->full_name])),
        students: @json($studentPayload),
        sheets: @json($sheetPayload),
    })">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <template x-for="card in summaryCards" :key="card.label">
                    <div class="rounded-xl bg-white border border-gray-100 shadow-sm p-5 flex flex-col gap-2">
                        <div class="text-xs font-semibold tracking-wide uppercase text-gray-500" x-text="card.label"></div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-bold text-gray-900" x-text="card.value"></span>
                            <span class="text-xs text-gray-500" x-text="card.sub"></span>
                        </div>
                        <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full" :class="card.bar" :style="`width: ${card.percent}%`"></div>
                        </div>
                    </div>
                </template>
            </div>

            <x-card>
                <div class="flex flex-col gap-4">
                    <div class="flex flex-wrap items-center gap-3 justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Input Cepat</h3>
                            <p class="text-sm text-gray-500">Pilih kelas, mapel, dan tanggal. Data masih dummy hingga API tersambung.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <select x-model="filters.classId" class="rounded-md border-gray-300 text-sm">
                                <option value="">Semua Kelas</option>
                                <template x-for="cls in classes" :key="cls.id">
                                    <option :value="cls.id" x-text="cls.name"></option>
                                </template>
                            </select>
                            <select x-model="filters.subjectId" class="rounded-md border-gray-300 text-sm">
                                <option value="">Semua Mapel</option>
                                <template x-for="sub in subjects" :key="sub.id">
                                    <option :value="sub.id" x-text="sub.name"></option>
                                </template>
                            </select>
                            <input type="date" x-model="filters.date" class="rounded-md border-gray-300 text-sm">
                            <button class="px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Terapkan</button>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-100 overflow-hidden">
                        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between">
                            <div class="text-sm font-semibold text-gray-800" x-text="activeSheet.title"></div>
                            <div class="flex items-center gap-2 text-xs text-gray-600">
                                <span class="px-2 py-1 rounded-full bg-green-50 text-green-700 border border-green-100">Draft</span>
                                <span x-text="activeSheet.date"></span>
                            </div>
                        </div>
                        <div class="divide-y divide-gray-100">
                            <template x-for="student in students" :key="student.id">
                                <div class="grid grid-cols-1 md:grid-cols-4 items-center px-4 py-3 gap-3">
                                    <div class="col-span-2">
                                        <div class="text-sm font-medium text-gray-900" x-text="student.name"></div>
                                        <div class="text-xs text-gray-500" x-text="student.nis"></div>
                                    </div>
                                    <div class="flex gap-2 flex-wrap">
                                        <template x-for="state in ['hadir','izin','sakit','alfa']" :key="state">
                                            <label class="flex items-center gap-1 text-xs text-gray-700 cursor-pointer">
                                                <input type="radio" class="text-indigo-600 border-gray-300" :name="`status-${student.id}`" :checked="student.status === state">
                                                <span class="capitalize" x-text="state"></span>
                                            </label>
                                        </template>
                                    </div>
                                    <input type="text" placeholder="Catatan (opsional)" class="rounded-md border-gray-200 text-sm px-3 py-2" :value="student.note">
                                </div>
                            </template>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between">
                            <div class="text-xs text-gray-500">Contoh tampilan input. Tombol di bawah masih statis.</div>
                            <div class="flex gap-2">
                                <button class="px-3 py-2 text-sm bg-gray-100 rounded-lg text-gray-700 hover:bg-gray-200">Simpan Draft</button>
                                <button class="px-3 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Kunci Sheet</button>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Riwayat Sheet</h3>
                        <p class="text-sm text-gray-500">Daftar 10 sheet terakhir (dummy). Akan disambungkan ke API attendance/sheets.</p>
                    </div>
                    <div class="flex gap-2">
                        <select class="rounded-md border-gray-300 text-sm">
                            <option>Filter Status</option>
                            <option>Draft</option>
                            <option>Terkunci</option>
                        </select>
                        <button class="px-3 py-2 text-sm bg-white border border-gray-200 rounded-lg hover:bg-gray-50">Export</button>
                    </div>
                </div>
                <x-table :headers="['Kelas', 'Mapel', 'Guru', 'Tanggal', 'Sesi', 'Status', 'Kehadiran']">
                    <template x-for="sheet in sheets" :key="sheet.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900" x-text="sheet.class"></td>
                            <td class="px-6 py-4 text-sm text-gray-700" x-text="sheet.subject"></td>
                            <td class="px-6 py-4 text-sm text-gray-700" x-text="sheet.teacher"></td>
                            <td class="px-6 py-4 text-sm text-gray-700" x-text="sheet.date"></td>
                            <td class="px-6 py-4 text-sm text-gray-700" x-text="sheet.session"></td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 rounded-md text-xs font-semibold"
                                      :class="sheet.locked ? 'bg-green-50 text-green-700 ring-1 ring-green-100' : 'bg-amber-50 text-amber-700 ring-1 ring-amber-100'">
                                    <span x-text="sheet.locked ? 'Terkunci' : 'Draft'"></span>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <span x-text="`${sheet.present}% Hadir`"></span>
                            </td>
                        </tr>
                    </template>
                </x-table>
            </x-card>
        </div>
    </div>

    <script>
        function attendancePage(initial) {
            return {
                filters: { classId: '', subjectId: '', date: (new Date()).toISOString().slice(0,10) },
                summaryCards: [
                    { label: 'Hadir', value: 92, sub: '% dari 310 siswa', percent: 92, bar: 'bg-green-500' },
                    { label: 'Izin', value: 3, sub: '%', percent: 3, bar: 'bg-amber-400' },
                    { label: 'Sakit', value: 4, sub: '%', percent: 4, bar: 'bg-sky-500' },
                    { label: 'Alfa', value: 1, sub: '%', percent: 1, bar: 'bg-rose-500' },
                ],
                classes: initial.classes || [],
                subjects: initial.subjects || [],
                teachers: initial.teachers || [],
                students: initial.students || [],
                activeSheet: {
                    title: (initial.sheets?.[0]?.class ?? 'Absensi') + (initial.sheets?.[0]?.subject ? ' - ' + initial.sheets[0].subject : ''),
                    date: initial.sheets?.[0]?.date ?? (new Date()).toISOString().slice(0,10),
                },
                sheets: initial.sheets || [],
            };
        }
    </script>
</x-app-layout>
