<x-table :headers="['Nama Lengkap', 'No HP / Email', 'Jumlah Anak', 'Aksi']">
    @forelse($guardians as $guardian)
        <tr>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ $guardian->full_name }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ $guardian->phone ?? '-' }}</div>
                <div class="text-sm text-gray-500">{{ $guardian->email ?? '-' }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ $guardian->students->count() }} siswa
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button @click="openDetail({{ $guardian }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</button>
                <button @click="openModal('edit', {{ $guardian }})" class="text-amber-600 hover:text-amber-900 mr-3">Edit</button>
                <button @click="deleteGuardian({{ $guardian->id }})" class="text-red-600 hover:text-red-900">Hapus</button>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                Belum ada data wali.
            </td>
        </tr>
    @endforelse
</x-table>

<div class="mt-4">
    {{ $guardians->links() }}
</div>
