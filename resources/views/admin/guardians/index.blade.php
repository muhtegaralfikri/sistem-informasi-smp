<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Data Orang Tua/Wali') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="guardianPage()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Orang Tua/Wali</h3>

                    <div class="flex items-center gap-4 w-full sm:w-auto">
                        <div class="relative w-full sm:w-64">
                            <x-text-input
                                type="text"
                                x-model="searchQuery"
                                @input.debounce.500ms="performSearch"
                                placeholder="Cari Nama, No HP, Email..."
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
                            + Tambah Wali
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

                <div id="guardian-list">
                    @include('admin.guardians.partials.table')
                </div>
            </x-card>
        </div>

        <!-- Guardian Form Modal (Create/Edit) -->
        <x-modal name="guardian-modal" focusable>
            <form @submit.prevent="saveGuardian" class="p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4" x-text="isEdit ? 'Edit Data Wali' : 'Tambah Wali Baru'"></h2>

                <div class="space-y-4">
                    <div>
                        <x-input-label value="Nama Lengkap" />
                        <x-text-input type="text" x-model="form.full_name" class="mt-1 block w-full" required />
                        <p class="text-sm text-red-600 mt-1" x-text="errors.full_name"></p>
                    </div>

                    <div>
                        <x-input-label value="No HP" />
                        <x-text-input type="text" x-model="form.phone" class="mt-1 block w-full" />
                        <p class="text-sm text-red-600 mt-1" x-text="errors.phone"></p>
                    </div>

                    <div>
                        <x-input-label value="Email" />
                        <x-text-input type="email" x-model="form.email" class="mt-1 block w-full" />
                        <p class="text-sm text-red-600 mt-1" x-text="errors.email"></p>
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
        <x-modal name="detail-guardian-modal" focusable>
            <div class="p-6" x-data="{ guardian: null }" @open-detail.window="guardian = $event.detail">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Detail Wali</h2>
                <template x-if="guardian">
                    <div class="space-y-3">
                        <div>
                            <span class="block text-xs text-gray-500 uppercase tracking-wider">Nama Lengkap</span>
                            <span class="block text-sm font-medium text-gray-900" x-text="guardian.full_name"></span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="block text-xs text-gray-500 uppercase tracking-wider">No HP</span>
                                <span class="block text-sm font-medium text-gray-900" x-text="guardian.phone || '-'"></span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-500 uppercase tracking-wider">Email</span>
                                <span class="block text-sm font-medium text-gray-900" x-text="guardian.email || '-'"></span>
                            </div>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500 uppercase tracking-wider">Jumlah Anak</span>
                            <span class="block text-sm font-medium text-gray-900" x-text="guardian.students?.length || 0 + ' siswa'"></span>
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
        function guardianPage() {
            return {
                isEdit: false,
                currentId: null,
                searchQuery: "{{ request('search') }}",
                loadingSearch: false,
                form: {
                    full_name: '',
                    phone: '',
                    email: '',
                },
                errors: {},
                loading: false,

                async performSearch() {
                    this.loadingSearch = true;
                    try {
                        const res = await fetch("{{ route('admin.guardians.index') }}?search=" + encodeURIComponent(this.searchQuery), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const html = await res.text();
                        document.getElementById('guardian-list').innerHTML = html;

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
                            phone: data.phone || '',
                            email: data.email || '',
                        };
                    } else {
                        this.currentId = null;
                        this.form = {
                            full_name: '',
                            phone: '',
                            email: '',
                        };
                    }
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'guardian-modal' }));
                },

                openDetail(data) {
                    window.dispatchEvent(new CustomEvent('open-detail', { detail: data }));
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'detail-guardian-modal' }));
                },

                async saveGuardian() {
                    this.loading = true;
                    this.errors = {};

                    const url = this.isEdit
                        ? "{{ route('admin.guardians.update', ':id') }}".replace(':id', this.currentId)
                        : "{{ route('admin.guardians.store') }}";

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

                        alert(this.isEdit ? 'Data berhasil diperbarui' : 'Wali berhasil ditambahkan');
                        window.location.reload();

                    } catch (error) {
                        console.error(error);
                        alert('Terjadi kesalahan jaringan');
                    } finally {
                        this.loading = false;
                    }
                },

                async deleteGuardian(id) {
                    if (!confirm('Apakah Anda yakin ingin menghapus data wali ini?')) return;

                    try {
                        const res = await fetch("{{ route('admin.guardians.destroy', ':id') }}".replace(':id', id), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'Accept': 'application/json'
                            }
                        });

                        if (res.ok) {
                            alert('Data wali berhasil dihapus');
                            this.performSearch();
                        } else {
                            alert('Gagal menghapus data');
                        }
                    } catch(e) {
                        alert('Terjadi kesalahan jaringan');
                    }
                },

                async exportData() {
                    try {
                        const res = await fetch("{{ route('admin.guardians.export') }}");
                        if (!res.ok) throw new Error('Gagal mengekspor data');

                        const blob = await res.blob();
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'wali_' + new Date().toISOString().slice(0,10) + '.xlsx';
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
                        const res = await fetch("{{ route('admin.guardians.import') }}", {
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

                        alert('Data wali berhasil diimport');
                        window.location.reload();
                    } catch (e) {
                        alert('Gagal mengimport data');
                    }
                }
            }
        }
    </script>
</x-app-layout>
