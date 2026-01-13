<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Dashboard Wali Kelas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-card>
                <div class="flex flex-col gap-3">
                    <h3 class="text-lg font-semibold text-gray-900">Ringkasan Kelas Anda</h3>
                    <p class="text-sm text-gray-500">Halaman ini akan menampilkan rekap absensi kelas, daftar raport yang perlu disetujui/publish, dan catatan siswa.</p>
                    <div class="rounded-lg border border-gray-100 p-4 bg-gray-50 text-sm text-gray-600">
                        Data masih dummy. Akan diisi otomatis berdasarkan wali kelas yang terdaftar di tabel classes.
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
