<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Data Kelas') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="classPage()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Kelas</h3>
                    <button @click="$dispatch('open-modal', 'create-class-modal')" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
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
                                <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</a>
                                <a href="#" class="text-amber-600 hover:text-amber-900">Edit</a>
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

        <!-- Create Class Modal -->
        <x-modal name="create-class-modal" focusable>
            <form @submit.prevent="storeClass" class="p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Tambah Kelas Baru</h2>
                
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
    </div>

    <script>
        function classPage() {
            return {
                form: {
                    name: '',
                    grade_level: '7',
                    homeroom_teacher_id: '',
                    semester_id: '',
                    major: ''
                },
                errors: {},
                loading: false,

                async storeClass() {
                    this.loading = true;
                    this.errors = {};
                    
                    try {
                        const res = await fetch('{{ route('admin.classes.store') }}', {
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
                        alert('Kelas berhasil ditambahkan');
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
