<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Data Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="studentPage()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Siswa</h3>
                    <button @click="openModal('create')" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        + Tambah Siswa
                    </button>
                </div>

                <x-table :headers="['Nama Lengkap', 'NIS / NISN', 'Kelas', 'Status', 'Aksi']">
                    @forelse($students as $student)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $student->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $student->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $student->nis }}</div>
                                <div class="text-sm text-gray-500">{{ $student->nisn }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $student->classRoom?->name ?? 'Belum ada kelas' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-badge :color="$student->status === 'active' ? 'green' : 'gray'">
                                    {{ $student->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button @click="openDetail({{ $student }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</button>
                                <button @click="openModal('edit', {{ $student }})" class="text-amber-600 hover:text-amber-900 mr-3">Edit</button>
                                <button @click="deleteStudent({{ $student->id }})" class="text-red-600 hover:text-red-900">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Belum ada data siswa.
                            </td>
                        </tr>
                    @endforelse
                </x-table>

                <div class="mt-4">
                    {{ $students->links() }}
                </div>
            </x-card>
        </div>

        <!-- Student Form Modal (Create/Edit) -->
        <x-modal name="student-modal" focusable>
            <form @submit.prevent="saveStudent" class="p-6">
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
                        ? '{{ route('admin.students.update', ':id') }}'.replace(':id', this.currentId)
                        : '{{ route('admin.students.store') }}';
                    
                    const method = this.isEdit ? 'PUT' : 'POST';

                    try {
                        const res = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
                        window.location.reload();
                        
                    } catch (error) {
                        console.error(error);
                        alert('Terjadi kesalahan jaringan');
                    } finally {
                        this.loading = false;
                    }
                },

                async deleteStudent(id) {
                    if (!confirm('Apakah Anda yakin ingin menghapus data siswa ini?')) return;

                    try {
                        const res = await fetch('{{ route('admin.students.destroy', ':id') }}'.replace(':id', id), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        if (res.ok) {
                            alert('Data siswa berhasil dihapus');
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
