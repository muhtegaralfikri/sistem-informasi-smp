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
                    <button @click="$dispatch('open-modal', 'create-student-modal')" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
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
                                <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</a>
                                <a href="#" class="text-amber-600 hover:text-amber-900">Edit</a>
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

        <!-- Create Student Modal -->
        <x-modal name="create-student-modal" focusable>
            <form @submit.prevent="storeStudent" class="p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Tambah Siswa Baru</h2>
                
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
    </div>

    <script>
        function studentPage() {
            return {
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

                async storeStudent() {
                    this.loading = true;
                    this.errors = {};
                    
                    try {
                        const res = await fetch('{{ route('admin.students.store') }}', {
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
                        alert('Siswa berhasil ditambahkan');
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
