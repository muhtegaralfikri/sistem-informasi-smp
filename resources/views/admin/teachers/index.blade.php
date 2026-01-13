<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Data Guru') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="teacherPage()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Guru</h3>

                    <div class="flex items-center gap-4 w-full sm:w-auto">
                        <button @click="openModal('create')" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 whitespace-nowrap">
                            + Tambah Guru
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

                <x-table :headers="['Nama Lengkap', 'NIP', 'Email / Kontak', 'Status', 'Aksi']">
                    @forelse($teachers as $teacher)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $teacher->full_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $teacher->nip ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $teacher->email ?? '-' }}</div>
                                <div class="text-sm text-gray-500">{{ $teacher->phone ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-badge :color="$teacher->status === 'active' ? 'green' : 'gray'">
                                    {{ $teacher->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button @click="openModal('edit', {{ $teacher }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                <button @click="deleteTeacher({{ $teacher->id }})" class="text-red-600 hover:text-red-900">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Belum ada data guru.
                            </td>
                        </tr>
                    @endforelse
                </x-table>

                <div class="mt-4">
                    {{ $teachers->links() }}
                </div>
            </x-card>
        </div>

         <!-- Teacher Form Modal -->
        <x-modal name="teacher-modal" focusable>
            <form @submit.prevent="saveTeacher" class="p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4" x-text="isEdit ? 'Edit Data Guru' : 'Tambah Guru Baru'"></h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-2">
                         <x-input-label value="Nama Lengkap" />
                         <x-text-input type="text" x-model="form.full_name" class="mt-1 block w-full" required />
                         <p class="text-sm text-red-600 mt-1" x-text="errors.full_name"></p>
                    </div>
                    
                    <div>
                        <x-input-label value="NIP" />
                         <x-text-input type="text" x-model="form.nip" class="mt-1 block w-full" />
                        <p class="text-sm text-red-600 mt-1" x-text="errors.nip"></p>
                    </div>

                    <div>
                        <x-input-label value="Email" />
                        <x-text-input type="email" x-model="form.email" class="mt-1 block w-full" required />
                        <p class="text-sm text-red-600 mt-1" x-text="errors.email"></p>
                    </div>

                    <div>
                        <x-input-label value="Nomor Telepon" />
                        <x-text-input type="text" x-model="form.phone" class="mt-1 block w-full" />
                         <p class="text-sm text-red-600 mt-1" x-text="errors.phone"></p>
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
    </div>

    <script>
        function teacherPage() {
            return {
                isEdit: false,
                currentId: null,
                form: {
                    full_name: '',
                    nip: '',
                    email: '',
                    phone: '',
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
                            nip: data.nip || '',
                            email: data.email || '',
                            phone: data.phone || '',
                            status: data.status
                        };
                    } else {
                        this.currentId = null;
                        this.form = {
                            full_name: '',
                            nip: '',
                            email: '',
                            phone: '',
                            status: 'active'
                        };
                    }
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'teacher-modal' }));
                },

                async saveTeacher() {
                    this.loading = true;
                    this.errors = {};
                    
                    const url = this.isEdit
                        ? "{{ route('admin.teachers.update', ':id') }}".replace(':id', this.currentId)
                        : "{{ route('admin.teachers.store') }}";
                    
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

                        alert(this.isEdit ? 'Data berhasil diperbarui' : 'Guru berhasil ditambahkan. Password default: password');
                        window.location.reload();
                        
                    } catch (error) {
                        console.error(error);
                        alert('Terjadi kesalahan jaringan');
                    } finally {
                        this.loading = false;
                    }
                },

                async deleteTeacher(id) {
                    if (!confirm('Apakah Anda yakin ingin menghapus data guru ini? Akun user terkait mungkin tidak terhapus otomatis (tergantung sistem).')) return;

                    try {
                        const res = await fetch("{{ route('admin.teachers.destroy', ':id') }}".replace(':id', id), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'Accept': 'application/json'
                            }
                        });

                        if (res.ok) {
                            alert('Data guru berhasil dihapus');
                            window.location.reload();
                        } else {
                            alert('Gagal menghapus data');
                        }
                    } catch(e) {
                         alert('Terjadi kesalahan jaringan');
                    }
                },

                async exportData() {
                    try {
                        const res = await fetch("{{ route('admin.teachers.export') }}");
                        if (!res.ok) {
                            const text = await res.text();
                            console.error('Export failed:', res.status, text);
                            throw new Error('Gagal mengekspor data: ' + res.status + ' ' + text);
                        }

                        const blob = await res.blob();
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'guru_' + new Date().toISOString().slice(0,10) + '.xlsx';
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                    } catch (e) {
                        console.error('Export error:', e);
                        alert('Gagal mengekspor data: ' + e.message);
                    }
                },

                async importData(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const formData = new FormData();
                    formData.append('file', file);

                    try {
                        const res = await fetch("{{ route('admin.teachers.import') }}", {
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

                        alert('Data guru berhasil diimport');
                        window.location.reload();
                    } catch (e) {
                        alert('Gagal mengimport data');
                    }
                }
            }
        }
    </script>
</x-app-layout>
