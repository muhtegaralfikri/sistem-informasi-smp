<x-app-layout>
    <div x-data="schedulePage()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                    <h3 class="text-lg font-medium text-gray-900">Jadwal Pelajaran</h3>
                    
                    <div class="w-full sm:w-64">
                        <select x-model="selectedClassId" @change="loadSchedules" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div x-show="!selectedClassId" class="text-center py-12 text-gray-500">
                    Silakan pilih kelas terlebih dahulu untuk melihat dan mengatur jadwal.
                </div>

                <div x-show="selectedClassId" style="display: none;">
                    <!-- Loading State -->
                    <div x-show="loading" class="text-center py-12">
                        <span class="text-gray-500">Memuat jadwal...</span>
                    </div>

                    <!-- Content -->
                    <div x-show="!loading">
                        
                        <!-- List Mapel per Kelas -->
                        <div class="mb-8">
                            <h4 class="text-md font-bold text-gray-700 mb-3">Daftar Mata Pelajaran Kelas Ini</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <template x-for="subject in subjects" :key="subject.class_subject_id">
                                    <div class="border rounded-lg p-4 bg-gray-50 hover:bg-white hover:shadow-md transition">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h5 class="font-bold text-gray-900" x-text="subject.subject_name"></h5>
                                                <p class="text-sm text-gray-500" x-text="subject.teacher_name"></p>
                                            </div>
                                            <button @click="openScheduleModal(subject)" class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded hover:bg-indigo-200">
                                                + Jadwal
                                            </button>
                                        </div>
                                        
                                        <!-- Existing Schedules for this subject -->
                                        <div class="mt-3 space-y-1">
                                            <template x-for="sch in subject.schedules" :key="sch.id">
                                                <div class="flex justify-between items-center text-xs bg-white border p-1.5 rounded">
                                                    <span>
                                                        <span class="font-semibold" x-text="formatDay(sch.day)"></span>, 
                                                        <span x-text="sch.start_time.substring(0,5) + ' - ' + sch.end_time.substring(0,5)"></span>
                                                    </span>
                                                    <button @click="deleteSchedule(sch.id)" class="text-red-500 hover:text-red-700">
                                                        &times;
                                                    </button>
                                                </div>
                                            </template>
                                            <div x-show="subject.schedules.length === 0" class="text-xs text-gray-400 italic">
                                                Belum ada jadwal
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <div x-show="subjects.length === 0" class="text-center py-4 text-gray-500 bg-gray-50 rounded-lg border border-dashed">
                                Belum ada mata pelajaran yang diassign ke kelas ini. 
                                <br>Silakan ke menu "Data Kelas" atau "Mata Pelajaran" untuk assign mapel dulu.
                            </div>
                        </div>

                    </div>
                </div>
            </x-card>
        </div>

        <!-- Schedule Modal -->
        <x-modal name="schedule-modal" focusable>
            <form @submit.prevent="saveSchedule" class="p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">
                    Tambah Jadwal: <span x-text="selectedSubject?.subject_name"></span>
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <x-input-label value="Hari" />
                        <select x-model="form.day" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="monday">Senin</option>
                            <option value="tuesday">Selasa</option>
                            <option value="wednesday">Rabu</option>
                            <option value="thursday">Kamis</option>
                            <option value="friday">Jumat</option>
                            <option value="saturday">Sabtu</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Jam Mulai" />
                            <input type="time" x-model="form.start_time" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        </div>
                        <div>
                            <x-input-label value="Durasi (JP)" />
                            <input type="number" x-model="form.duration_jp" min="1" max="10" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <p class="text-xs text-gray-500 mt-1">1 JP = 40 Menit</p>
                        </div>
                    </div>

                    <div class="bg-indigo-50 p-3 rounded text-sm text-indigo-700 flex justify-between items-center" x-show="form.start_time && form.duration_jp">
                        <span>Estimasi Selesai:</span>
                        <span class="font-bold" x-text="calculateEndTime()"></span>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Batal
                    </button>
                    <x-primary-button ::disabled="submitting">
                        <span x-show="!submitting">Simpan</span>
                        <span x-show="submitting">Menyimpan...</span>
                    </x-primary-button>
                </div>
            </form>
        </x-modal>
    </div>

    <script>
        function schedulePage() {
            return {
                selectedClassId: '',
                subjects: [],
                loading: false,
                submitting: false,
                selectedSubject: null,
                form: {
                    day: 'monday',
                    start_time: '07:00',
                    duration_jp: 2
                },

                async loadSchedules() {
                    if (!this.selectedClassId) return;
                    
                    this.loading = true;
                    try {
                        const res = await fetch(`/admin/schedules/class/${this.selectedClassId}`);
                        this.subjects = await res.json();
                    } catch (e) {
                        console.error(e);
                        alert('Gagal memuat jadwal');
                    } finally {
                        this.loading = false;
                    }
                },

                openScheduleModal(subject) {
                    this.selectedSubject = subject;
                    this.form = {
                        day: 'monday',
                        start_time: '07:00',
                        duration_jp: 2
                    };
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'schedule-modal' }));
                },

                calculateEndTime() {
                    if (!this.form.start_time || !this.form.duration_jp) return '-';
                    
                    const [hours, minutes] = this.form.start_time.split(':').map(Number);
                    const totalMinutes = (hours * 60) + minutes + (this.form.duration_jp * 40);
                    
                    const endHours = Math.floor(totalMinutes / 60) % 24;
                    const endMinutes = totalMinutes % 60;
                    
                    return `${String(endHours).padStart(2, '0')}:${String(endMinutes).padStart(2, '0')}`;
                },

                formatDay(day) {
                    const days = {
                        'monday': 'Senin',
                        'tuesday': 'Selasa',
                        'wednesday': 'Rabu',
                        'thursday': 'Kamis',
                        'friday': 'Jumat',
                        'saturday': 'Sabtu',
                        'sunday': 'Minggu'
                    };
                    return days[day] || day;
                },

                async saveSchedule() {
                    this.submitting = true;
                    try {
                        const res = await fetch("{{ route('admin.schedules.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                class_subject_id: this.selectedSubject.class_subject_id,
                                ...this.form
                            })
                        });

                        const data = await res.json();

                        if (!res.ok) {
                            alert(data.message || 'Gagal menyimpan jadwal');
                            return;
                        }

                        // Refresh data
                        await this.loadSchedules();
                        window.dispatchEvent(new CustomEvent('close-modal', { detail: 'schedule-modal' }));
                        this.$dispatch('close');

                    } catch (e) {
                        console.error(e);
                        alert('Terjadi kesalahan jaringan');
                    } finally {
                        this.submitting = false;
                    }
                },

                async deleteSchedule(id) {
                    if (!confirm('Hapus jadwal ini?')) return;

                    try {
                        const res = await fetch(`/admin/schedules/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'Accept': 'application/json'
                            }
                        });

                        if (res.ok) {
                            await this.loadSchedules();
                        } else {
                            alert('Gagal menghapus jadwal');
                        }
                    } catch (e) {
                        alert('Terjadi kesalahan jaringan');
                    }
                }
            }
        }
    </script>
</x-app-layout>
