<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Data Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="studentPage()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Siswa</h3>
                    
                    <div class="flex items-center gap-4 w-full sm:w-auto">
                        <div class="relative w-full sm:w-64">
                            <x-text-input 
                                type="text" 
                                x-model="searchQuery"
                                @input.debounce.500ms="performSearch"
                                placeholder="Cari Nama, NIS, Kelas..." 
                                class="w-full"
                            />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg x-show="loadingSearch" class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>

                        <button @click="openModal('create')" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 whitespace-nowrap">
                            + Tambah Siswa
                        </button>
                        <button @click="exportData" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none whitespace-nowrap">
                            Export Excel
                        </button>
                        <button @click="$refs.importInput.click()" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none whitespace-nowrap">
                            Import Excel
                        </button>
                        <input type="file" x-ref="importInput" @change="importData" accept=".xlsx,.xls,.csv" class="hidden">
                    </div>
                </div>

                <div id="student-list">
                    @include('admin.students.partials.table')
                </div>
            </x-card>
        </div>

        <!-- Student Form Modal (Create/Edit) -->
        <x-modal name="student-modal" focusable>
            <form @submit.prevent="saveStudent" class="p-6">
                <!-- ... Form Content ... -->
                <h2 class="text-lg font-bold text-gray-900 mb-4" x-text="isEdit ? 'Edit Data Siswa' : 'Tambah Siswa Baru'"></h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-2">
                         <x-input-label value="Nama Lengkap" />
                         <x-text-input type="text" x-model="form.full_name" class="mt-1 block w-full" required />
                         <p class="text-sm text-red-600 mt-1" x-text="errors.full_name"></p>
                    </div>
                    
                    <div>
                        <x-input-label value="NIS" />
                        <x-text-input type="text" x-model="form.nis" class="mt-1 block w-full" required />
                        <p class="text-sm text-red-600 mt-1" x-text="errors.nis"></p>
                    </div>

                    <div>
                        <x-input-label value="NISN" />
                        <x-text-input type="text" x-model="form.nisn" class="mt-1 block w-full" required />
                        <p class="text-sm text-red-600 mt-1" x-text="errors.nisn"></p>
                    </div>

                    <div>
                        <x-input-label value="Jenis Kelamin" />
                        <select x-model="form.gender" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="male">Laki-laki</option>
                            <option value="female">Perempuan</option>
                        </select>
                         <p class="text-sm text-red-600 mt-1" x-text="errors.gender"></p>
                    </div>

                    <div>
                        <x-input-label value="Tanggal Lahir" />
                        <x-text-input type="date" x-model="form.birth_date" class="mt-1 block w-full" />
                         <p class="text-sm text-red-600 mt-1" x-text="errors.birth_date"></p>
                    </div>

                    <div>
                        <x-input-label value="Kelas" />
                        <select x-model="form.class_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-sm text-red-600 mt-1" x-text="errors.class_id"></p>
                    </div>
                     <div>
                        <x-input-label value="Status" />
                        <select x-model="form.status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                         <p class="text-sm text-red-600 mt-1" x-text="errors.status"></p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                     <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Batal
                    </button>
                    <x-primary-button ::disabled="loading">
                        <span x-show="!loading">Simpan</span>
                        <span x-show="loading">Menyimpan...</span>
                    </x-primary-button>
                </div>
            </form>
        </x-modal>

        <!-- Detail Modal -->
        <x-modal name="detail-student-modal" focusable>
            <!-- ... Detail Content ... -->
             <div class="p-6" x-data="{ student: null }" @open-detail.window="student = $event.detail">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Detail Siswa</h2>
                <template x-if="student">
                    <div class="space-y-3">
                         <div>
                            <span class="block text-xs text-gray-500 uppercase tracking-wider">Nama Lengkap</span>
                            <span class="block text-sm font-medium text-gray-900" x-text="student.full_name"></span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="block text-xs text-gray-500 uppercase tracking-wider">NIS</span>
                                <span class="block text-sm font-medium text-gray-900" x-text="student.nis"></span>
                            </div>
                             <div>
                                <span class="block text-xs text-gray-500 uppercase tracking-wider">NISN</span>
                                <span class="block text-sm font-medium text-gray-900" x-text="student.nisn"></span>
                            </div>
                        </div>
                         <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="block text-xs text-gray-500 uppercase tracking-wider">Jenis Kelamin</span>
                                <span class="block text-sm font-medium text-gray-900" x-text="student.gender == 'male' ? 'Laki-laki' : 'Perempuan'"></span>
                            </div>
                              <div>
                                <span class="block text-xs text-gray-500 uppercase tracking-wider">Tanggal Lahir</span>
                                <span class="block text-sm font-medium text-gray-900" x-text="student.birth_date || '-'"></span>
                            </div>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500 uppercase tracking-wider">Kelas</span>
                            <span class="block text-sm font-medium text-gray-900" x-text="student.class_room?.name || 'Belum ada kelas'"></span>
                        </div>
                         <div>
                            <span class="block text-xs text-gray-500 uppercase tracking-wider">Status</span>
                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20" x-show="student.status === 'active'">Aktif</span>
                            <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10" x-show="student.status !== 'active'">Nonaktif</span>
                        </div>
                    </div>
                </template>
                 <div class="mt-6 flex justify-end">
                     <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Tutup
                    </button>
                </div>
            </div>
        </x-modal>
    </div>

    <script>
        function studentPage() {
            return {
                isEdit: false,
                currentId: null,
                searchQuery: "{{ request('search') }}",
                loadingSearch: false,
                form: {
                    full_name: '',
                    nis: '',
                    nisn: '',
                    gender: 'male',
                    birth_date: '',
                    class_id: '',
                    status: 'active'
                },
                errors: {},
                loading: false,

                async performSearch() {
                    this.loadingSearch = true;
                    try {
                        const res = await fetch("{{ route('admin.students.index') }}?search=" + encodeURIComponent(this.searchQuery), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const html = await res.text();
                        document.getElementById('student-list').innerHTML = html;
                        
                        // Update URL without reload
                        const newUrl = new URL(window.location);
                        if (this.searchQuery) {
                            newUrl.searchParams.set('search', this.searchQuery);
                        } else {
                            newUrl.searchParams.delete('search');
                        }
                        window.history.pushState({}, '', newUrl);

                    } catch (e) {
                        console.error('Search failed', e);
                    } finally {
                        this.loadingSearch = false;
                    }
                },

                openModal(type, data = null) {
                    this.isEdit = type === 'edit';
                    this.errors = {};
                    
                    if (this.isEdit && data) {
                        this.currentId = data.id;
                        this.form = {
                            full_name: data.full_name,
                            nis: data.nis,
                            nisn: data.nisn,
                            gender: data.gender,
                            birth_date: data.birth_date ? data.birth_date.split('T')[0] : '',
                            class_id: data.class_id || '',
                            status: data.status
                        };
                    } else {
                        this.currentId = null;
                        this.form = {
                            full_name: '',
                            nis: '',
                            nisn: '',
                            gender: 'male',
                            birth_date: '',
                            class_id: '',
                            status: 'active'
                        };
                    }
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'student-modal' }));
                },

                openDetail(data) {
                    window.dispatchEvent(new CustomEvent('open-detail', { detail: data }));
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'detail-student-modal' }));
                },

                async saveStudent() {
                    this.loading = true;
                    this.errors = {};
                    
                    const url = this.isEdit
                        ? "{{ route('admin.students.update', ':id') }}".replace(':id', this.currentId)
                        : "{{ route('admin.students.store') }}";
                    
                    const method = this.isEdit ? 'PUT' : 'POST';

                    try {
                        const res = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            body: JSON.stringify(this.form)
                        });

                        const data = await res.json();

                        if (!res.ok) {
                            if (res.status === 422) {
                                this.errors = data.errors;
                            } else {
                                alert(data.message || 'Terjadi kesalahan');
                            }
                            return;
                        }

                        alert(this.isEdit ? 'Data berhasil diperbarui' : 'Siswa berhasil ditambahkan');
                        
                        // Smart reload: just refresh the table instead of full page
                        this.performSearch(); 
                        this.openModal('close'); // Actually close modal logic is via dispatch usually
                        window.dispatchEvent(new CustomEvent('close-modal', { detail: 'student-modal' })); 
                        this.$dispatch('close'); // Close the modal in Alpine way if inside x-data scope of modal, but this is root scope.
                        // Since modal has x-on:click="$dispatch('close')", we can simulate that or just use location.reload if lazy.
                        // I will stick to window.location.reload() for now to be safe with modal states, 
                        // unless I want to implement seamless update which is better but riskier with modal states.
                        // Let's stick to reload for Create/Edit, but Search is live.
                        window.location.reload(); 
                        
                    } catch (error) {
                        console.error(error);
                        alert('Terjadi kesalahan jaringan');
                    } finally {
                        this.loading = false;
                    }
                },
                // ... deleteStudent ...
                async deleteStudent(id) {
                    if (!confirm('Apakah Anda yakin ingin menghapus data siswa ini?')) return;

                    try {
                        const res = await fetch("{{ route('admin.students.destroy', ':id') }}".replace(':id', id), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'Accept': 'application/json'
                            }
                        });

                        if (res.ok) {
                            alert('Data siswa berhasil dihapus');
                            // window.location.reload();
                            this.performSearch(); // Refresh list without reload
                        } else {
                            alert('Gagal menghapus data');
                        }
                    } catch(e) {
                         alert('Terjadi kesalahan jaringan');
                    }
                },

                async exportData() {
                    try {
                        const res = await fetch("{{ route('admin.students.export') }}");
                        if (!res.ok) throw new Error('Gagal mengekspor data');

                        const blob = await res.blob();
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'siswa_' + new Date().toISOString().slice(0,10) + '.xlsx';
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                    } catch (e) {
                        alert('Gagal mengekspor data');
                    }
                },

                async importData(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const formData = new FormData();
                    formData.append('file', file);

                    try {
                        const res = await fetch("{{ route('admin.students.import') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'Accept': 'application/json',
                            },
                            body: formData,
                        });

                        const data = await res.json();
                        if (!res.ok) {
                            alert(data.message || 'Gagal mengimport data');
                            return;
                        }

                        alert('Data siswa berhasil diimport');
                        window.location.reload();
                    } catch (e) {
                        alert('Gagal mengimport data');
                    }
                }
            }
        }
    </script>
</x-app-layout>
