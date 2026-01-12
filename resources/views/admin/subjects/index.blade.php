<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Mata Pelajaran') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="subjectPage()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Katalog Mapel</h3>
                    <button @click="openModal('create')" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        + Tambah Mapel
                    </button>
                </div>

                <x-table :headers="['Kode', 'Nama Mata Pelajaran', 'KKM', 'Aksi']">
                    @forelse($subjects as $subject)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500">
                                {{ $subject->code }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $subject->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $subject->passing_grade }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button @click="openDetail({{ $subject }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</button>
                                <button @click="openModal('edit', {{ $subject }})" class="text-amber-600 hover:text-amber-900 mr-3">Edit</button>
                                <button @click="deleteSubject({{ $subject->id }})" class="text-red-600 hover:text-red-900">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Belum ada data mata pelajaran.
                            </td>
                        </tr>
                    @endforelse
                </x-table>

                <div class="mt-4">
                    {{ $subjects->links() }}
                </div>
            </x-card>
        </div>

        <!-- Subject Form Modal (Create/Edit) -->
        <x-modal name="subject-modal" focusable>
            <form @submit.prevent="saveSubject" class="p-6">
                 <h2 class="text-lg font-bold text-gray-900 mb-4" x-text="isEdit ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran'"></h2>
                
                <div class="space-y-4">
                    <div>
                         <x-input-label value="Kode Mapel (Misal: MTK-01)" />
                         <x-text-input type="text" x-model="form.code" class="mt-1 block w-full" required />
                         <p class="text-sm text-red-600 mt-1" x-text="errors.code"></p>
                    </div>

                    <div>
                         <x-input-label value="Nama Mata Pelajaran" />
                         <x-text-input type="text" x-model="form.name" class="mt-1 block w-full" required />
                         <p class="text-sm text-red-600 mt-1" x-text="errors.name"></p>
                    </div>

                    <div>
                         <x-input-label value="KKM (Nilai Minimum)" />
                         <x-text-input type="number" x-model="form.passing_grade" class="mt-1 block w-full" min="0" max="100" />
                         <p class="text-sm text-red-600 mt-1" x-text="errors.passing_grade"></p>
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
        <x-modal name="detail-subject-modal" focusable>
            <div class="p-6" x-data="{ subject: null }" @open-detail.window="subject = $event.detail">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Detail Mata Pelajaran</h2>
                <template x-if="subject">
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="block text-xs text-gray-500 uppercase tracking-wider">Kode Mapel</span>
                                <span class="block text-sm font-medium text-gray-900" x-text="subject.code"></span>
                            </div>
                             <div>
                                <span class="block text-xs text-gray-500 uppercase tracking-wider">KKM</span>
                                <span class="block text-sm font-medium text-gray-900" x-text="subject.passing_grade"></span>
                            </div>
                        </div>
                         <div>
                            <span class="block text-xs text-gray-500 uppercase tracking-wider">Nama Mata Pelajaran</span>
                            <span class="block text-sm font-medium text-gray-900" x-text="subject.name"></span>
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
        function subjectPage() {
            return {
                isEdit: false,
                currentId: null,
                form: {
                    code: '',
                    name: '',
                    passing_grade: 75
                },
                errors: {},
                loading: false,

                openModal(type, data = null) {
                    this.isEdit = type === 'edit';
                    this.errors = {};
                    
                    if (this.isEdit && data) {
                        this.currentId = data.id;
                        this.form = {
                            code: data.code,
                            name: data.name,
                            passing_grade: data.passing_grade
                        };
                    } else {
                        this.currentId = null;
                        this.form = {
                            code: '',
                            name: '',
                            passing_grade: 75
                        };
                    }
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'subject-modal' }));
                },

                openDetail(data) {
                    window.dispatchEvent(new CustomEvent('open-detail', { detail: data }));
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'detail-subject-modal' }));
                },

                async saveSubject() {
                    this.loading = true;
                    this.errors = {};
                    
                    const url = this.isEdit 
                        ? '{{ route('admin.subjects.update', ':id') }}'.replace(':id', this.currentId)
                        : '{{ route('admin.subjects.store') }}';
                    
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

                        alert(this.isEdit ? 'Data berhasil diperbarui' : 'Mata Pelajaran berhasil ditambahkan');
                        window.location.reload();
                        
                    } catch (error) {
                        console.error(error);
                        alert('Terjadi kesalahan jaringan');
                    } finally {
                        this.loading = false;
                    }
                },

                async deleteSubject(id) {
                    if (!confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?')) return;

                    try {
                        const res = await fetch('{{ route('admin.subjects.destroy', ':id') }}'.replace(':id', id), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        if (res.ok) {
                            alert('Mata Pelajaran berhasil dihapus');
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
