<x-app-layout>


    <div x-data="announcementPage({
        classes: @json($classes->map(fn($c) => ['id' => $c->id, 'name' => $c->name])),
        announcements: @json($announcements),
    })">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Buat Pengumuman</h3>
                        <p class="text-sm text-gray-500">Form dummy. Akan dihubungkan ke model `announcements`.</p>
                    </div>
                    <span class="text-xs text-gray-500">Auto-save belum aktif</span>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Judul" />
                            <x-text-input type="text" x-model="form.title" class="mt-1 block w-full" placeholder="Contoh: Ujian Tengah Semester" />
                        </div>
                        <div>
                            <x-input-label value="Target" />
                            <select x-model="form.target_scope" class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                                <option value="all">Semua</option>
                                <option value="class">Kelas Tertentu</option>
                                <option value="parents">Orang Tua</option>
                            </select>
                        </div>
                    </div>
                    <div x-show="form.target_scope === 'class'">
                        <x-input-label value="Pilih Kelas" />
                        <select x-model="form.class_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                            <option value="">-- Pilih --</option>
                            <template x-for="cls in classes" :key="cls.id">
                                <option :value="cls.id" x-text="cls.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Isi Pengumuman" />
                        <textarea x-model="form.body" rows="4" class="mt-1 block w-full rounded-md border-gray-300 text-sm" placeholder="Tulis informasi penting..."></textarea>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer select-none">
                            <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" x-model="form.publish_now">
                            <span>Publikasikan sekarang</span>
                        </label>
                        <div class="flex gap-2">
                            <button class="px-3 py-2 bg-gray-100 text-gray-800 rounded-lg text-sm hover:bg-gray-200">Simpan Draft</button>
                            <button class="px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Kirim</button>
                        </div>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Riwayat Pengumuman</h3>
                        <p class="text-sm text-gray-500">Daftar contoh. Tombol edit/hapus belum aktif.</p>
                    </div>
                    <select class="rounded-md border-gray-300 text-sm">
                        <option value="">Filter Target</option>
                        <option value="all">Semua</option>
                        <option value="class">Per Kelas</option>
                        <option value="parents">Orang Tua</option>
                    </select>
                </div>
                <div class="space-y-3">
                    <template x-for="item in announcements" :key="item.id">
                        <div class="rounded-lg border border-gray-100 p-4 bg-white hover:bg-gray-50">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold text-gray-900" x-text="item.title"></span>
                                        <span class="text-xs px-2 py-1 rounded-md border border-gray-200 text-gray-700" x-text="item.target"></span>
                                        <span class="text-xs px-2 py-1 rounded-md border border-gray-200 text-gray-700" x-text="item.published_at"></span>
                                    </div>
                                    <p class="text-sm text-gray-700 mt-1" x-text="item.body"></p>
                                </div>
                                <div class="flex gap-2 text-sm">
                                    <button class="text-indigo-600 hover:underline">Edit</button>
                                    <button class="text-red-600 hover:underline">Hapus</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </x-card>
        </div>
    </div>

    <script>
        function announcementPage(initial) {
            return {
                form: {
                    title: '',
                    target_scope: 'all',
                    class_id: '',
                    body: '',
                    publish_now: true,
                },
                classes: initial.classes || [],
                announcements: initial.announcements || [],
            };
        }
    </script>
</x-app-layout>
