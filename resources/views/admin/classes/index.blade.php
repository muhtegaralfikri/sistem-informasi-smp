<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Data Kelas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Kelas</h3>
                    <button class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
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
    </div>
</x-app-layout>
