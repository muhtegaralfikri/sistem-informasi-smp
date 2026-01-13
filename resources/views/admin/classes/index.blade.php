<x-app-layout>


    <div x-data="classPage()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Kelas</h3>
                    <button @click="openModal('create')" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        + Tambah Kelas
                    </button>
                </div>

                <x-table :headers="['Nama Kelas', 'Tingkat', 'Wali Kelas', 'Semester', 'Aksi']">
                    @forelse($classes as $class)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-gray-900">{{ $class->name }}</span>
                                @if($class->major)
                                    <span class="text-xs text-gray-500 ml-1">({{ $class->major }})</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Kelas {{ $class->grade_level }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $class->homeroomTeacher?->full_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $class->semester?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button @click="openDetail({{ $class }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</button>
                                <button @click="openManageSubjects({{ $class }})" class="text-green-600 hover:text-green-900 mr-3">Atur Mapel</button>
                                <button @click="openModal('edit', {{ $class }})" class="text-amber-600 hover:text-amber-900 mr-3">Edit</button>
                                <button @click="deleteClass({{ $class->id }})" class="text-red-600 hover:text-red-900">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Belum ada data kelas.
                            </td>
                        </tr>
                    @endforelse
                </x-table>

                <div class="mt-4">
                    {{ $classes->links() }}
                </div>
            </x-card>
        </div>

        <!-- Class Form Modal (Create/Edit) -->
        <x-modal name="class-modal" focusable>
            <form @submit.prevent="saveClass" class="p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4" x-text="isEdit ? 'Edit Data Kelas' : 'Tambah Kelas Baru'"></h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                         <x-input-label value="Nama Kelas (Contoh: VII-A)" />
                         <x-text-input type="text" x-model="form.name" class="mt-1 block w-full" required />
                         <p class="text-sm text-red-600 mt-1" x-text="errors.name"></p>
                    </div>
                    
                    <div>
                        <x-input-label value="Tingkat (7, 8, 9)" />
                         <select x-model="form.grade_level" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="7">7 (VII)</option>
                            <option value="8">8 (VIII)</option>
                            <option value="9">9 (IX)</option>
                         </select>
                        <p class="text-sm text-red-600 mt-1" x-text="errors.grade_level"></p>
                    </div>

                    <div>
                        <x-input-label value="Wali Kelas" />
                        <select x-model="form.homeroom_teacher_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">-- Pilih Wali Kelas --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                            @endforeach
                        </select>
                        <p class="text-sm text-red-600 mt-1" x-text="errors.homeroom_teacher_id"></p>
                    </div>

                    <div>
                        <x-input-label value="Semester" />
                        <select x-model="form.semester_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">-- Pilih Semester --</option>
                            @foreach($semesters as $semester)
                                <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                            @endforeach
                        </select>
                         <p class="text-sm text-red-600 mt-1" x-text="errors.semester_id"></p>
                    </div>

                     <div class="col-span-2">
                        <x-input-label value="Jurusan (Opsional)" />
                        <x-text-input type="text" x-model="form.major" class="mt-1 block w-full" placeholder="Kosongkan jika SMP umum" />
                         <p class="text-sm text-red-600 mt-1" x-text="errors.major"></p>
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
        <x-modal name="detail-class-modal" focusable>
            <div class="p-6" x-data="{ classData: null }" @open-detail.window="classData = $event.detail">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Detail Kelas</h2>
                <template x-if="classData">
                    <div class="space-y-3">
                         <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="block text-xs text-gray-500 uppercase tracking-wider">Nama Kelas</span>
                                <span class="block text-sm font-medium text-gray-900" x-text="classData.name"></span>
                            </div>
                             <div>
                                <span class="block text-xs text-gray-500 uppercase tracking-wider">Tingkat</span>
                                <span class="block text-sm font-medium text-gray-900" x-text="'Kelas ' + classData.grade_level"></span>
                            </div>
                        </div>
                         <div>
                            <span class="block text-xs text-gray-500 uppercase tracking-wider">Wali Kelas</span>
                            <span class="block text-sm font-medium text-gray-900" x-text="classData.homeroom_teacher?.full_name || '-'"></span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500 uppercase tracking-wider">Semester</span>
                             <span class="block text-sm font-medium text-gray-900" x-text="classData.semester?.name || '-'"></span>
                        </div>
                         <div x-show="classData.major">
                             <span class="block text-xs text-gray-500 uppercase tracking-wider">Jurusan</span>
                            <span class="block text-sm font-medium text-gray-900" x-text="classData.major"></span>
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

        <!-- Manage Subjects Modal -->
        <x-modal name="manage-subjects-modal" focusable>
            <div class="p-6" x-data="{ 
                classData: null, 
                subjects: [], 
                form: { subject_id: '', teacher_id: '' },
                loading: false,
                adding: false,

                init() {
                    this.$watch('classData', value => {
                        if (value) this.fetchSubjects();
                    });
                },

                async fetchSubjects() {
                    if (!this.classData) return;
                    this.loading = true;
                    try {
                        const res = await fetch(`/admin/class-subjects/class/${this.classData.id}`);
                        this.subjects = await res.json();
                    } catch (e) {
                         alert('Gagal memuat data mapel');
                    } finally {
                        this.loading = false;
                    }
                },

                async addSubject() {
                    this.adding = true;
                    try {
                        const res = await fetch(`{{ route('admin.class-subjects.store') }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': `{{ csrf_token() }}`,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                class_id: this.classData.id,
                                subject_id: this.form.subject_id,
                                teacher_id: this.form.teacher_id
                            })
                        });

                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || 'Gagal menambahkan');
                        
                        this.form.subject_id = ''; 
                        this.form.teacher_id = '';
                        this.fetchSubjects();
                    } catch (e) {
                        alert(e.message);
                    } finally {
                        this.adding = false;
                    }
                },

                async deleteSubject(id) {
                    if(!confirm('Hapus mapel ini dari kelas?')) return;
                    
                    try {
                        const res = await fetch(`/admin/class-subjects/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': `{{ csrf_token() }}`,
                                'Accept': 'application/json'
                            }
                        });

                        if(res.ok) this.fetchSubjects();
                        else alert('Gagal menghapus');
                    } catch (e) {
                        alert('Terjadi kesalahan jaringan');
                    }
                }
            }" 
            @open-manage-subjects.window="
                classData = $event.detail; 
                subjects = []; 
                form = { subject_id: '', teacher_id: '' };
                fetchSubjects();
            ">
                <h2 class="text-lg font-bold text-gray-900 mb-4">
                    Atur Mapel: <span x-text="classData?.name"></span>
                </h2>

                <!-- Add Subject Form -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6 border">
                    <h3 class="text-sm font-bold text-gray-700 mb-3">Tambah Mapel ke Kelas Ini</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                        <div>
                            <select x-model="form.subject_id" class="w-full text-sm border-gray-300 rounded-md">
                                <option value="">-- Pilih Mapel --</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select x-model="form.teacher_id" class="w-full text-sm border-gray-300 rounded-md">
                                <option value="">-- Pilih Guru (Opsional) --</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button @click="addSubject" :disabled="!form.subject_id || adding" class="px-3 py-1.5 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700 disabled:opacity-50">
                            <span x-show="!adding">+ Tambahkan</span>
                            <span x-show="adding">Menambahkan...</span>
                        </button>
                    </div>
                </div>

                <!-- Existing Subjects List -->
                <div>
                    <h3 class="text-sm font-bold text-gray-700 mb-3">Daftar Mapel Terdaftar</h3>
                    <div x-show="loading" class="text-center py-4 text-gray-500">Memuat...</div>
                    
                    <div x-show="!loading && subjects.length === 0" class="text-center py-4 text-gray-400 italic border rounded bg-white">
                        Belum ada mapel di kelas ini.
                    </div>

                    <ul x-show="!loading && subjects.length > 0" class="divide-y border rounded bg-white max-h-60 overflow-y-auto">
                        <template x-for="item in subjects" :key="item.id">
                            <li class="p-3 flex justify-between items-center hover:bg-gray-50">
                                <div>
                                    <div class="font-medium text-gray-900" x-text="item.subject.name"></div>
                                    <div class="text-xs text-gray-500" x-text="item.teacher ? item.teacher.full_name : 'Belum ada guru'"></div>
                                </div>
                                <button @click="deleteSubject(item.id)" class="text-red-500 hover:text-red-700 text-xs px-2 py-1 border border-red-200 rounded">
                                    Hapus
                                </button>
                            </li>
                        </template>
                    </ul>
                </div>

                <div class="mt-6 flex justify-end">
                     <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Tutup
                    </button>
                </div>
            </div>
        </x-modal>
    </div>

    <script>
        function classPage() {
            return {
                isEdit: false,
                currentId: null,
                form: {
                    name: '',
                    grade_level: '7',
                    homeroom_teacher_id: '',
                    semester_id: '',
                    major: ''
                },
                errors: {},
                loading: false,

                openModal(type, data = null) {
                    this.isEdit = type === 'edit';
                    this.errors = {};
                    
                    if (this.isEdit && data) {
                        this.currentId = data.id;
                        this.form = {
                            name: data.name,
                            grade_level: data.grade_level,
                            homeroom_teacher_id: data.homeroom_teacher_id || '',
                            semester_id: data.semester_id || '',
                            major: data.major || ''
                        };
                    } else {
                        this.currentId = null;
                        this.form = {
                            name: '',
                            grade_level: '7',
                            homeroom_teacher_id: '',
                            semester_id: '',
                            major: ''
                        };
                    }
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'class-modal' }));
                },

                openDetail(data) {
                    window.dispatchEvent(new CustomEvent('open-detail', { detail: data }));
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'detail-class-modal' }));
                },

                openManageSubjects(data) {
                    window.dispatchEvent(new CustomEvent('open-manage-subjects', { detail: data }));
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'manage-subjects-modal' }));
                },

                async saveClass() {
                    this.loading = true;
                    this.errors = {};
                    
                    const url = this.isEdit
                        ? "{{ route('admin.classes.update', ':id') }}".replace(':id', this.currentId)
                        : "{{ route('admin.classes.store') }}";
                    
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

                        alert(this.isEdit ? 'Data berhasil diperbarui' : 'Kelas berhasil ditambahkan');
                        window.location.reload();
                        
                    } catch (error) {
                        console.error(error);
                        alert('Terjadi kesalahan jaringan');
                    } finally {
                        this.loading = false;
                    }
                },

                 async deleteClass(id) {
                    if (!confirm('Apakah Anda yakin ingin menghapus kelas ini?')) return;

                    try {
                        const res = await fetch("{{ route('admin.classes.destroy', ':id') }}".replace(':id', id), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'Accept': 'application/json'
                            }
                        });

                        if (res.ok) {
                            alert('Data kelas berhasil dihapus');
                            window.location.reload();
                        } else {
                            alert('Gagal menghapus data');
                        }
                    } catch(e) {
                         alert('Terjadi kesalahan jaringan');
                    }
                }
            }
        }
    </script>
</x-app-layout>
