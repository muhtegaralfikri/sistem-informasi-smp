<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Admin Panel') }}
        </h2>
    </x-slot>

    <div x-data="adminDashboard()">
        <div class="max-w-7xl mx-auto space-y-8">
            
            <!-- Welcome Section -->
            <div class="relative overflow-hidden rounded-2xl bg-indigo-600 px-6 py-8 shadow-xl sm:px-10 sm:py-12">
                <div class="relative z-10">
                    <h2 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">Selamat Datang, Admin!</h2>
                    <p class="mt-2 text-indigo-100">Kelola data akademik, siswa, guru, dan jadwal pelajaran dalam satu panel terintegrasi.</p>
                </div>
                <div class="absolute -right-6 -top-6 h-32 w-32 rounded-full bg-indigo-500/20 blur-2xl"></div>
                <div class="absolute -bottom-6 right-10 h-32 w-32 rounded-full bg-indigo-400/20 blur-2xl"></div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <template x-for="stat in stats" :key="stat.label">
                    <div class="group relative overflow-hidden rounded-xl bg-white p-5 shadow-sm transition-all hover:shadow-md border border-gray-100">
                        <dt class="truncate text-xs font-semibold uppercase tracking-wider text-gray-500" x-text="stat.label"></dt>
                        <dd class="mt-2 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-gray-900" x-text="stat.value"></span>
                        </dd>
                        <div class="absolute right-0 top-0 p-3 opacity-10 transition-opacity group-hover:opacity-20">
                            <!-- Placeholder Icon based on label -->
                            <svg class="h-12 w-12 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                            </svg>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Main Content Area -->
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                
                <!-- Tahun Ajaran Card -->
                <div class="flex flex-col rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                        <h3 class="flex items-center gap-2 text-base font-semibold leading-6 text-gray-900">
                            <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                            </svg>
                            Tahun Ajaran
                        </h3>
                        <button @click="$dispatch('open-modal', 'create-year-modal')" class="rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-100">
                            + Tambah Data
                        </button>
                    </div>
                     <div class="flex-1 overflow-hidden p-0">
                        <template x-if="years.length === 0">
                            <div class="flex flex-col items-center justify-center py-10 text-center">
                                <div class="rounded-full bg-gray-50 p-3">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                                <h3 class="mt-2 text-sm font-semibold text-gray-900">Tidak ada data</h3>
                                <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan tahun ajaran baru.</p>
                            </div>
                        </template>
                        <ul x-show="years.length > 0" class="divide-y divide-gray-100">
                             <template x-for="year in years" :key="year.id">
                                <li class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-x-3">
                                            <p class="text-sm font-semibold leading-6 text-gray-900" x-text="year.name"></p>
                                             <span x-show="year.is_active" class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Aktif</span>
                                        </div>
                                        <div class="mt-1 flex items-center gap-x-2 text-xs leading-5 text-gray-500">
                                            <span x-text="year.start_date"></span>
                                            <svg viewBox="0 0 2 2" class="h-0.5 w-0.5 fill-current"><circle cx="1" cy="1" r="1" /></svg>
                                            <span x-text="year.end_date"></span>
                                        </div>
                                    </div>
                                    <div class="flex flex-none items-center gap-x-4">
                                        <button class="hidden rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:block">Edit</button>
                                    </div>
                                </li>
                             </template>
                        </ul>
                    </div>
                </div>

                <!-- Semester Card -->
                <div class="flex flex-col rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                        <h3 class="flex items-center gap-2 text-base font-semibold leading-6 text-gray-900">
                            <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Semester
                        </h3>
                         <button @click="$dispatch('open-modal', 'create-semester-modal')" class="rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-100">
                            + Tambah Data
                        </button>
                    </div>
                    <div class="flex-1 overflow-hidden p-0">
                         <template x-if="semesters.length === 0">
                            <div class="flex flex-col items-center justify-center py-10 text-center">
                                <div class="rounded-full bg-gray-50 p-3">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                                <h3 class="mt-2 text-sm font-semibold text-gray-900">Tidak ada data</h3>
                                <p class="mt-1 text-sm text-gray-500">Tambahkan semester untuk tahun ajaran yang aktif.</p>
                            </div>
                        </template>
                         <ul x-show="semesters.length > 0" class="divide-y divide-gray-100">
                             <template x-for="sem in semesters" :key="sem.id">
                                <li class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-x-3">
                                            <p class="text-sm font-semibold leading-6 text-gray-900" x-text="sem.name"></p>
                                             <span x-show="sem.is_active" class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Aktif</span>
                                        </div>
                                        <div class="mt-1 flex items-center gap-x-2 text-xs leading-5 text-gray-500">
                                            <span x-text="sem.academic_year?.name"></span>
                                            <svg viewBox="0 0 2 2" class="h-0.5 w-0.5 fill-current"><circle cx="1" cy="1" r="1" /></svg>
                                            <span x-text="`${sem.start_date} - ${sem.end_date}`"></span>
                                        </div>
                                    </div>
                                    <div class="flex flex-none items-center gap-x-4">
                                        <button class="hidden rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:block">Edit</button>
                                    </div>
                                </li>
                             </template>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals -->
        <x-modal name="create-year-modal" focusable>
            <form @submit.prevent="createYear" class="p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Tambah Tahun Ajaran Baru</h2>
                
                <div class="space-y-4">
                    <div>
                        <x-input-label value="Nama Tahun (Contoh: 2025/2026)" />
                        <x-text-input type="text" x-model="formYear.name" class="mt-1 block w-full" placeholder="2025/2026" required />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Mulai" />
                            <x-text-input type="date" x-model="formYear.start_date" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label value="Selesai" />
                            <x-text-input type="date" x-model="formYear.end_date" class="mt-1 block w-full" required />
                        </div>
                    </div>
                    <div>
                         <label class="inline-flex items-center space-x-2 text-sm text-gray-700 cursor-pointer select-none">
                            <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" x-model="formYear.is_active">
                            <span>Set sebagai Tahun Ajaran Aktif</span>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex justify-between items-center bg-gray-50 -mx-6 -mb-6 px-6 py-4">
                     <button type="button" x-on:click="$dispatch('close')" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                        Batal
                    </button>
                    <div class="flex items-center gap-3">
                        <p class="text-sm text-red-600" x-text="errors.year"></p>
                        <x-primary-button ::disabled="loading">
                            <span x-show="!loading">Simpan Data</span>
                            <span x-show="loading">...</span>
                        </x-primary-button>
                    </div>
                </div>
            </form>
        </x-modal>

        <x-modal name="create-semester-modal" focusable>
             <form @submit.prevent="createSemester" class="p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Tambah Semester Baru</h2>

                <div class="space-y-4">
                    <div>
                        <x-input-label value="Tahun Ajaran" />
                        <select class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" x-model="formSem.academic_year_id" required>
                            <option value="">-- Pilih Tahun --</option>
                            <template x-for="year in years" :key="year.id">
                                <option :value="year.id" x-text="year.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Nama Semester (Contoh: Ganjil / Genap)" />
                        <x-text-input type="text" x-model="formSem.name" class="mt-1 block w-full" placeholder="Ganjil" required />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                             <x-input-label value="Mulai" />
                            <x-text-input type="date" x-model="formSem.start_date" class="mt-1 block w-full" required />
                        </div>
                        <div>
                             <x-input-label value="Selesai" />
                            <x-text-input type="date" x-model="formSem.end_date" class="mt-1 block w-full" required />
                        </div>
                    </div>
                     <div>
                         <label class="inline-flex items-center space-x-2 text-sm text-gray-700 cursor-pointer select-none">
                            <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" x-model="formSem.is_active">
                            <span>Set sebagai Semester Aktif</span>
                        </label>
                    </div>
                </div>

                 <div class="mt-6 flex justify-between items-center bg-gray-50 -mx-6 -mb-6 px-6 py-4">
                     <button type="button" x-on:click="$dispatch('close')" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                        Batal
                    </button>
                    <div class="flex items-center gap-3">
                        <p class="text-sm text-red-600" x-text="errors.semester"></p>
                        <x-primary-button ::disabled="loading">
                            <span x-show="!loading">Simpan Data</span>
                            <span x-show="loading">...</span>
                        </x-primary-button>
                    </div>
                </div>
            </form>
        </x-modal>

    </div>

    <!-- Logic Script (Same logic) -->
    <script>
        function adminDashboard() {
            return {
                stats: [
                    { label: 'Siswa', value: {{ $stats['students'] }} },
                    { label: 'Guru', value: {{ $stats['teachers'] }} },
                    { label: 'Kelas', value: {{ $stats['classes'] }} },
                    { label: 'Mapel', value: {{ $stats['subjects'] }} },
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
                    
                    const formData = new FormData();
                    formData.append('name', this.formYear.name);
                    formData.append('start_date', this.formYear.start_date);
                    formData.append('end_date', this.formYear.end_date);
                    formData.append('is_active', this.formYear.is_active ? '1' : '0');

                    try {
                        const res = await fetch('{{ route('admin.academic-years.store') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: formData,
                        });
                        if (!res.ok) {
                            const data = await res.json();
                            throw new Error(data.message || 'Gagal simpan');
                        }
                        const data = await res.json();
                        this.years.unshift(data);
                        this.formYear = { name: '', start_date: '', end_date: '', is_active: false };
                        window.dispatchEvent(new CustomEvent('close-modal', { detail: 'create-year-modal' }));
                    } catch (e) {
                        this.errors.year = e.message;
                    } finally {
                        this.loading = false;
                    }
                },

                async createSemester() {
                    this.errors.semester = '';
                    this.loading = true;
                    
                    const formData = new FormData();
                    formData.append('academic_year_id', this.formSem.academic_year_id);
                    formData.append('name', this.formSem.name);
                    formData.append('start_date', this.formSem.start_date);
                    formData.append('end_date', this.formSem.end_date);
                    formData.append('is_active', this.formSem.is_active ? '1' : '0');

                    try {
                        const res = await fetch('{{ route('admin.semesters.store') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: formData,
                        });
                        if (!res.ok) {
                            const data = await res.json();
                            throw new Error(data.message || 'Gagal simpan');
                        }
                        const data = await res.json();
                        this.semesters.unshift(data);
                        this.formSem = { academic_year_id: '', name: '', start_date: '', end_date: '', is_active: false };
                        window.dispatchEvent(new CustomEvent('close-modal', { detail: 'create-semester-modal' }));
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
