<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Data Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Siswa</h3>
                    <button class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        + Tambah Siswa
                    </button>
                    <!-- Note: Create Modal/Page logic will be added later or handled via AlpineJS if preferred -->
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
    </div>
</x-app-layout>
