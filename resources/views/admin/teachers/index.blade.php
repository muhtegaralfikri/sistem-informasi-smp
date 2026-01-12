<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Data Guru') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="teacherPage()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Guru</h3>
                    <button @click="$dispatch('open-modal', 'create-teacher-modal')" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        + Tambah Guru
                    </button>
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
                                <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
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

         <!-- Create Teacher Modal -->
        <x-modal name="create-teacher-modal" focusable>
            <form @submit.prevent="storeTeacher" class="p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Tambah Guru Baru</h2>
                
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
                form: {
                    full_name: '',
                    nip: '',
                    email: '',
                    phone: '',
                    status: 'active'
                },
                errors: {},
                loading: false,

                async storeTeacher() {
                    this.loading = true;
                    this.errors = {};
                    
                    try {
                        const res = await fetch('{{ route('admin.teachers.store') }}', {
                            method: 'POST',
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

                        // Success
                        alert('Guru berhasil ditambahkan. Password default: password');
                        window.location.reload();
                        
                    } catch (error) {
                        console.error(error);
                        alert('Terjadi kesalahan jaringan');
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
