<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8" x-data="adminDashboard()">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900">Admin Panel</h1>
            <p class="text-sm text-gray-500">Kelola data master, absensi, penilaian, raport.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <template x-for="stat in stats" :key="stat.label">
                <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500" x-text="stat.label"></p>
                    <p class="mt-2 text-2xl font-bold text-gray-900" x-text="stat.value"></p>
                </div>
            </template>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Tahun Ajaran</h2>
                    <span class="text-xs text-gray-500">Aktif: satu saja</span>
                </div>
                <form class="mt-4 space-y-3" @submit.prevent="createYear">
                    <div>
                        <label class="text-sm text-gray-700">Nama</label>
                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 shadow-sm" x-model="formYear.name" placeholder="2024/2025">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm text-gray-700">Mulai</label>
                            <input type="date" class="mt-1 w-full rounded-md border-gray-300 shadow-sm" x-model="formYear.start_date">
                        </div>
                        <div>
                            <label class="text-sm text-gray-700">Selesai</label>
                            <input type="date" class="mt-1 w-full rounded-md border-gray-300 shadow-sm" x-model="formYear.end_date">
                        </div>
                    </div>
                    <label class="inline-flex items-center space-x-2 text-sm text-gray-700">
                        <input type="checkbox" class="rounded border-gray-300" x-model="formYear.is_active">
                        <span>Set aktif</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700" :disabled="loading">
                            <span x-show="!loading">Simpan</span>
                            <span x-show="loading">Memproses...</span>
                        </button>
                        <p class="text-sm text-red-600" x-text="errors.year"></p>
                    </div>
                </form>
                <div class="mt-4 divide-y divide-gray-100 border-t border-gray-100" >
                    <template x-for="year in years" :key="year.id">
                        <div class="py-2 flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900" x-text="year.name"></p>
                                <p class="text-xs text-gray-500" x-text="`${year.start_date} - ${year.end_date}`"></p>
                            </div>
                            <span class="text-xs font-semibold" :class="year.is_active ? 'text-green-600' : 'text-gray-400'">
                                <span x-text="year.is_active ? 'Aktif' : 'Nonaktif'"></span>
                            </span>
                        </div>
                    </template>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Semester</h2>
                    <span class="text-xs text-gray-500">Terikat tahun ajaran</span>
                </div>
                <form class="mt-4 space-y-3" @submit.prevent="createSemester">
                    <div>
                        <label class="text-sm text-gray-700">Tahun Ajaran</label>
                        <select class="mt-1 w-full rounded-md border-gray-300 shadow-sm" x-model="formSem.academic_year_id">
                            <option value="">Pilih tahun</option>
                            <template x-for="year in years" :key="year.id">
                                <option :value="year.id" x-text="year.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm text-gray-700">Nama Semester</label>
                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 shadow-sm" x-model="formSem.name" placeholder="Ganjil / Genap">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm text-gray-700">Mulai</label>
                            <input type="date" class="mt-1 w-full rounded-md border-gray-300 shadow-sm" x-model="formSem.start_date">
                        </div>
                        <div>
                            <label class="text-sm text-gray-700">Selesai</label>
                            <input type="date" class="mt-1 w-full rounded-md border-gray-300 shadow-sm" x-model="formSem.end_date">
                        </div>
                    </div>
                    <label class="inline-flex items-center space-x-2 text-sm text-gray-700">
                        <input type="checkbox" class="rounded border-gray-300" x-model="formSem.is_active">
                        <span>Set aktif</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700" :disabled="loading">
                            <span x-show="!loading">Simpan</span>
                            <span x-show="loading">Memproses...</span>
                        </button>
                        <p class="text-sm text-red-600" x-text="errors.semester"></p>
                    </div>
                </form>
                <div class="mt-4 divide-y divide-gray-100 border-t border-gray-100">
                    <template x-for="sem in semesters" :key="sem.id">
                        <div class="py-2 flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900" x-text="sem.name"></p>
                                <p class="text-xs text-gray-500" x-text="`${sem.academic_year?.name || ''} • ${sem.start_date} - ${sem.end_date}`"></p>
                            </div>
                            <span class="text-xs font-semibold" :class="sem.is_active ? 'text-green-600' : 'text-gray-400'">
                                <span x-text="sem.is_active ? 'Aktif' : 'Nonaktif'"></span>
                            </span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
        function adminDashboard() {
            return {
                stats: [
                    { label: 'Siswa', value: {{ $stats['students'] }} },
                    { label: 'Guru', value: {{ $stats['teachers'] }} },
                    { label: 'Kelas', value: {{ $stats['classes'] }} },
                    { label: 'Mapel', value: {{ $stats['subjects'] }} },
                    { label: 'Orang Tua', value: {{ $stats['guardians'] }} },
                ],
                years: @json($years),
                semesters: @json($semesters->map(fn($s) => $s->setRelation('academicYear', $s->academicYear))),
                formYear: { name: '', start_date: '', end_date: '', is_active: false },
                formSem: { academic_year_id: '', name: '', start_date: '', end_date: '', is_active: false },
                errors: { year: '', semester: '' },
                loading: false,
                async createYear() {
                    this.errors.year = '';
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route('admin.academic-years.store') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: new FormData(Object.assign(document.createElement('form'), this.formYear)),
                        });
                        if (!res.ok) {
                            const data = await res.json();
                            throw new Error(data.message || 'Gagal simpan');
                        }
                        const data = await res.json();
                        this.years.unshift(data);
                        this.formYear = { name: '', start_date: '', end_date: '', is_active: false };
                    } catch (e) {
                        this.errors.year = e.message;
                    } finally {
                        this.loading = false;
                    }
                },
                async createSemester() {
                    this.errors.semester = '';
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route('admin.semesters.store') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: new FormData(Object.assign(document.createElement('form'), this.formSem)),
                        });
                        if (!res.ok) {
                            const data = await res.json();
                            throw new Error(data.message || 'Gagal simpan');
                        }
                        const data = await res.json();
                        this.semesters.unshift(data);
                        this.formSem = { academic_year_id: '', name: '', start_date: '', end_date: '', is_active: false };
                    } catch (e) {
                        this.errors.semester = e.message;
                    } finally {
                        this.loading = false;
                    }
                },
            };
        }
    </script>
</x-app-layout>
