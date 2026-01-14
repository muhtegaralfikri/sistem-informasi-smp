<x-app-layout>


    <div x-data="attendancePage({
        classes: {{ Illuminate\Support\Js::from($classes->map(fn($c) => ['id' => $c->id, 'name' => $c->name])) }},
        subjects: {{ Illuminate\Support\Js::from($subjects->map(fn($s) => ['id' => $s->id, 'name' => $s->name])) }},
        teachers: {{ Illuminate\Support\Js::from($teachers->map(fn($t) => ['id' => $t->id, 'name' => $t->full_name])) }},
        students: {{ Illuminate\Support\Js::from($students->map(fn($st) => ['id' => $st->id, 'name' => $st->full_name, 'nis' => $st->nis])) }},
        sheets: {{ Illuminate\Support\Js::from($sheetsData) }},
        apiBaseUrl: '{{ $apiBaseUrl }}',
    })">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Summary Cards -->
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

            <!-- Create Sheet Section -->
            <x-card>
                <div class="flex flex-col gap-4">
                    <div class="flex flex-wrap items-center gap-3 justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Buat Sheet Absensi</h3>
                            <p class="text-sm text-gray-500">Pilih kelas, mapel, dan tanggal untuk membuat sheet absensi baru.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                            <select x-model="newSheet.class_id" class="w-full rounded-md border-gray-300 text-sm">
                                <option value="">Pilih Kelas</option>
                                <template x-for="cls in classes" :key="cls.id">
                                    <option :value="cls.id" x-text="cls.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mapel</label>
                            <select x-model="newSheet.subject_id" class="w-full rounded-md border-gray-300 text-sm">
                                <option value="">Semua Mapel / Upacara</option>
                                <template x-for="sub in subjects" :key="sub.id">
                                    <option :value="sub.id" x-text="sub.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                            <input type="date" x-model="newSheet.date" class="w-full rounded-md border-gray-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sesi</label>
                            <input type="text" x-model="newSheet.session" placeholder="Contoh: 1-2" class="w-full rounded-md border-gray-300 text-sm">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button @click="createSheet" :disabled="loading" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 disabled:opacity-50">
                            <span x-show="!loading">Buat Sheet</span>
                            <span x-show="loading">Memproses...</span>
                        </button>
                    </div>
                    <p x-show="error" class="text-red-600 text-sm" x-text="error"></p>
                </div>
            </x-card>

            <!-- Input Attendance Section -->
            <x-card x-show="activeSheet.id">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-wrap items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Input Absensi</h3>
                            <p class="text-sm text-gray-500" x-text="`${activeSheet.class} - ${activeSheet.subject} (${activeSheet.date})`"></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold"
                                  :class="activeSheet.locked ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-amber-50 text-amber-700 border border-amber-100'">
                                <span x-text="activeSheet.locked ? 'Terkunci' : 'Draft'"></span>
                            </span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template x-for="student in students" :key="student.id">
                                    <tr>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900" x-text="student.full_name || student.name"></div>
                                        <div class="text-xs text-gray-500" x-text="student.nis || student.nisn"></div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <select
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            :value="getRecordStatus(student.id)"
                                            :disabled="activeSheet.locked"
                                            @change="updateRecordStatus(student.id, $event.target.value)"
                                            :class="{
                                                'bg-green-50 text-green-700 border-green-200': getRecordStatus(student.id) === 'hadir',
                                                'bg-blue-50 text-blue-700 border-blue-200': getRecordStatus(student.id) === 'izin',
                                                'bg-yellow-50 text-yellow-700 border-yellow-200': getRecordStatus(student.id) === 'sakit',
                                                'bg-red-50 text-red-700 border-red-200': getRecordStatus(student.id) === 'alfa',
                                                'text-gray-500': !getRecordStatus(student.id)
                                            }"
                                        >
                                            <option value="" disabled>Pilih Status</option>
                                            <option value="hadir">Hadir</option>
                                            <option value="izin">Izin</option>
                                            <option value="sakit">Sakit</option>
                                            <option value="alfa">Alfa</option>
                                        </select>
                                    </td>
                                        <td class="px-4 py-3">
                                            <input type="text"
                                                   placeholder="Catatan"
                                                   class="w-full rounded-md border-gray-200 text-sm"
                                                   :value="getRecordNote(student.id)"
                                                   :disabled="activeSheet.locked"
                                                   @input="updateRecordNote(student.id, $event.target.value)">
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-between items-center">
                        <p class="text-xs text-gray-500">
                            <span x-text="`${savedCount}/${students.length} siswa`"></span>
                        </p>
                        <div class="flex gap-2">
                            <button @click="saveRecords" :disabled="loading || activeSheet.locked"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 disabled:opacity-50">
                                <span x-show="!loading">Simpan Absensi</span>
                                <span x-show="loading">Menyimpan...</span>
                            </button>
                            <button @click="lockSheet" :disabled="loading || activeSheet.locked"
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 disabled:opacity-50">
                                <span x-show="!loading">Kunci Sheet</span>
                                <span x-show="loading">Mengunci...</span>
                            </button>
                        </div>
                    </div>
                    <p x-show="saveError" class="text-red-600 text-sm" x-text="saveError"></p>
                    <p x-show="saveSuccess" class="text-green-600 text-sm" x-text="saveSuccess"></p>
                </div>
            </x-card>

            <!-- History Section -->
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Riwayat Sheet</h3>
                        <p class="text-sm text-gray-500">Daftar sheet absensi yang pernah dibuat</p>
                    </div>
                    <div class="flex gap-2">
                        <button @click="loadSheets" class="px-3 py-2 text-sm bg-white border border-gray-200 rounded-lg hover:bg-gray-50">Refresh</button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mapel</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="sheet in sheets" :key="sheet.id">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900" x-text="sheet.class"></td>
                                    <td class="px-6 py-4 text-sm text-gray-700" x-text="sheet.subject || '-'"></td>
                                    <td class="px-6 py-4 text-sm text-gray-700" x-text="sheet.date"></td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-2 py-1 rounded-md text-xs font-semibold"
                                              :class="sheet.locked ? 'bg-green-50 text-green-700 ring-1 ring-green-100' : 'bg-amber-50 text-amber-700 ring-1 ring-amber-100'">
                                            <span x-text="sheet.locked ? 'Terkunci' : 'Draft'"></span>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm flex gap-2">
                                        <button @click="selectSheet(sheet)" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                            <span x-show="activeSheet.id === sheet.id">Aktif</span>
                                            <span x-show="activeSheet.id !== sheet.id">Pilih</span>
                                        </button>
                                        <button @click="deleteSheet(sheet.id)" class="text-red-500 hover:text-red-700 ml-2" :disabled="sheet.locked" title="Hapus Sheet">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>

    <script>
        function attendancePage(initial) {
            return {
                apiBaseUrl: initial.apiBaseUrl || '/admin',
                classes: initial.classes || [],
                subjects: initial.subjects || [],
                teachers: initial.teachers || [],
                students: initial.students || [],
                sheets: initial.sheets || [],
                activeSheet: {},
                records: {},

                newSheet: {
                    class_id: '',
                    subject_id: '',
                    date: new Date().toISOString().slice(0, 10),
                    session: '',
                },

                summaryCards: [
                    { label: 'Hadir', value: '-', sub: '', percent: 0, bar: 'bg-green-500' },
                    { label: 'Izin', value: '-', sub: '', percent: 0, bar: 'bg-amber-400' },
                    { label: 'Sakit', value: '-', sub: '', percent: 0, bar: 'bg-sky-500' },
                    { label: 'Alfa', value: '-', sub: '', percent: 0, bar: 'bg-rose-500' },
                ],

                loading: false,
                error: null,
                saveError: null,
                saveSuccess: null,
                savedCount: 0,

                init() {
                    if (this.sheets.length > 0) {
                        this.selectSheet(this.sheets[0]);
                    }
                    this.updateSummary();
                },

                async createSheet() {
                    this.loading = true;
                    this.error = null;

                    try {
                        const response = await fetch(`${this.apiBaseUrl}/attendance/sheets`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify(this.newSheet),
                        });

                        if (!response.ok) {
                            const data = await response.json();
                            throw new Error(data.message || 'Gagal membuat sheet');
                        }

                        const sheet = await response.json();
                        this.sheets.unshift(sheet);
                        this.selectSheet(sheet);

                        // Reset form
                        this.newSheet = {
                            class_id: '',
                            subject_id: '',
                            date: new Date().toISOString().slice(0, 10),
                            session: '',
                        };
                    } catch (e) {
                        this.error = e.message;
                    } finally {
                        this.loading = false;
                    }
                },

                async selectSheet(sheet) {
                    this.activeSheet = sheet;
                    this.records = {};
                    this.savedCount = 0;
                    this.saveSuccess = null;

                    if (!sheet.id) return;

                    this.loading = true;
                    try {
                        const response = await fetch(`${this.apiBaseUrl}/attendance/sheets/${sheet.id}`, {
                            headers: {
                                'Accept': 'application/json',
                            },
                        });

                        if (!response.ok) throw new Error('Gagal memuat data');

                        const data = await response.json();
                        this.activeSheet = data;

                        // Load existing records
                        if (data.records) {
                            data.records.forEach(record => {
                                this.records[record.student_id] = {
                                    status: record.status,
                                    note: record.note,
                                };
                            });
                            this.savedCount = data.records.length;
                        }

                        // Update students with class filter
                        if (sheet.class_id) {
                            this.loadStudents(sheet.class_id);
                        }

                        this.updateSummary();
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.loading = false;
                    }
                },

                async loadStudents(classId) {
                    try {
                        const response = await fetch(`${this.apiBaseUrl}/students?class_id=${classId}`, {
                            headers: { 'Accept': 'application/json' },
                        });
                        if (response.ok) {
                            const data = await response.json();
                            this.students = data.data || data;
                        }
                    } catch (e) {
                        console.error(e);
                    }
                },

                async saveRecords() {
                    this.loading = true;
                    this.saveError = null;
                    this.saveSuccess = null;

                    const recordsArray = this.students.map(student => ({
                        student_id: student.id,
                        status: this.getRecordStatus(student.id),
                        note: this.getRecordNote(student.id)
                    }));

                    try {
                        const response = await fetch(`${this.apiBaseUrl}/attendance/sheets/${this.activeSheet.id}/records`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ records: recordsArray }),
                        });

                        if (!response.ok) {
                            const data = await response.json();
                            throw new Error(data.message || 'Gagal menyimpan');
                        }

                        const data = await response.json();
                        this.records = {};
                        if (data.records) {
                            data.records.forEach(record => {
                                this.records[record.student_id] = {
                                    status: record.status,
                                    note: record.note,
                                };
                            });
                        }
                        this.savedCount = recordsArray.length;
                        this.saveSuccess = 'Data berhasil disimpan!';
                        this.updateSummary();
                    } catch (e) {
                        this.saveError = e.message;
                    } finally {
                        this.loading = false;
                    }
                },

                async lockSheet() {
                    if (!confirm('Yakin ingin mengunci sheet? Data tidak dapat diubah setelah dikunci.')) return;

                    this.loading = true;
                    try {
                        const response = await fetch(`${this.apiBaseUrl}/attendance/sheets/${this.activeSheet.id}/lock`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                        });

                        if (!response.ok) throw new Error('Gagal mengunci sheet');

                        const data = await response.json();
                        this.activeSheet.locked = !!data.locked_at;

                        // Update sheets list
                        const index = this.sheets.findIndex(s => s.id === data.id);
                        if (index !== -1) {
                            this.sheets[index] = data;
                        }
                    } catch (e) {
                        this.error = e.message;
                    } finally {
                        this.loading = false;
                    }
                },

                async deleteSheet(sheetId) {
                    if (!confirm('Apakah Anda yakin ingin menghapus sheet ini? Data yang dihapus tidak dapat dikembalikan.')) return;

                    this.loading = true;
                    try {
                        const response = await fetch(`${this.apiBaseUrl}/attendance/sheets/${sheetId}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                        });

                        if (!response.ok) {
                            const data = await response.json();
                            throw new Error(data.message || 'Gagal menghapus sheet');
                        }

                        // Remove from list
                        this.sheets = this.sheets.filter(s => s.id !== sheetId);

                        // If active sheet was deleted, clear active sheet
                        if (this.activeSheet && this.activeSheet.id === sheetId) {
                            this.activeSheet = {};
                            this.students = [];
                            this.records = {};
                        }
                    } catch (e) {
                        alert(e.message);
                    } finally {
                        this.loading = false;
                    }
                },

                async loadSheets() {
                    try {
                        const response = await fetch(`${this.apiBaseUrl}/attendance/sheets`, {
                            headers: { 'Accept': 'application/json' },
                        });
                        if (response.ok) {
                            this.sheets = await response.json();
                        }
                    } catch (e) {
                        console.error(e);
                    }
                },

                getRecordStatus(studentId) {
                    return this.records[studentId]?.status || 'hadir';
                },

                getRecordNote(studentId) {
                    return this.records[studentId]?.note || '';
                },

                updateRecordStatus(studentId, status) {
                    if (!this.records[studentId]) {
                        this.records[studentId] = { status: 'hadir', note: '' };
                    }
                    this.records[studentId].status = status;
                },

                updateRecordNote(studentId, note) {
                    if (!this.records[studentId]) {
                        this.records[studentId] = { status: 'hadir', note: '' };
                    }
                    this.records[studentId].note = note;
                },

                updateSummary() {
                    const records = Object.values(this.records);
                    const total = records.length || 1;

                    const counts = {
                        hadir: records.filter(r => r.status === 'hadir').length,
                        izin: records.filter(r => r.status === 'izin').length,
                        sakit: records.filter(r => r.status === 'sakit').length,
                        alfa: records.filter(r => r.status === 'alfa').length,
                    };

                    this.summaryCards = [
                        { label: 'Hadir', value: counts.hadir, sub: `${Math.round(counts.hadir/total*100)}%`, percent: Math.round(counts.hadir/total*100), bar: 'bg-green-500' },
                        { label: 'Izin', value: counts.izin, sub: `${Math.round(counts.izin/total*100)}%`, percent: Math.round(counts.izin/total*100), bar: 'bg-amber-400' },
                        { label: 'Sakit', value: counts.sakit, sub: `${Math.round(counts.sakit/total*100)}%`, percent: Math.round(counts.sakit/total*100), bar: 'bg-sky-500' },
                        { label: 'Alfa', value: counts.alfa, sub: `${Math.round(counts.alfa/total*100)}%`, percent: Math.round(counts.alfa/total*100), bar: 'bg-rose-500' },
                    ];
                },
            };
        }
    </script>
</x-app-layout>