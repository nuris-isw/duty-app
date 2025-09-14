<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-brand-dark leading-tight">
            {{ __('Manajemen Jabatan') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-2xl p-6">
                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                
                {{-- Tombol Tambah --}}
                <div class="mb-6 flex justify-end">
                    <a href="{{ route('admin.jabatan.create') }}" class="px-4 py-2 rounded-lg bg-brand-accent text-black text-xs font-semibold uppercase hover:bg-red-700 transition">
                        + Tambah Jabatan
                    </a>
                </div>

                {{-- Tabel Data --}}
                <div class="overflow-x-auto rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300 text-sm">
                        <thead class="bg-brand-dark text-black">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium">Nama Jabatan</th>
                                <th class="px-6 py-3 text-left font-medium">Bidang Kerja</th>
                                <th class="px-6 py-3 text-left font-medium">Alias</th>
                                <th class="px-6 py-3 text-left font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($jabatans as $jabatan)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">{{ $jabatan->nama_jabatan }}</td>
                                    <td class="px-6 py-4">{{ $jabatan->bidang_kerja }}</td>
                                    <td class="px-6 py-4">{{ $jabatan->alias }}</td>
                                    <td class="px-6 py-4 flex items-center space-x-3">
                                        <a href="{{ route('admin.jabatan.edit', $jabatan) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">Edit</a>
                                        <p>---</p>
                                        <form action="{{ route('admin.jabatan.destroy', $jabatan) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        Belum ada data Jabatan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>