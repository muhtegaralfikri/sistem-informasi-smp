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
                <button @click="openDetail({{ $student }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</button>
                <button @click="openModal('edit', {{ $student }})" class="text-amber-600 hover:text-amber-900 mr-3">Edit</button>
                <button @click="deleteStudent({{ $student->id }})" class="text-red-600 hover:text-red-900">Hapus</button>
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
