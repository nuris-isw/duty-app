<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-900 dark:text-neutral-100 leading-tight">
            {{ __('Manajemen Unit Kerja') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-neutral-800 shadow-md rounded-2xl p-6 sm:p-8 overflow-hidden">
                
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 text-green-700 border-l-4 border-green-400 rounded-lg" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                
                {{-- Tombol Tambah menggunakan komponen --}}
                <div class="mb-6 flex justify-end">
                    <x-primary-button onclick="window.location='{{ route('admin.unit-kerja.create') }}'">
                        + Tambah Unit Kerja
                    </x-primary-button>
                </div>

                {{-- Tabel Data dengan gaya grid --}}
                <div class="overflow-x-auto border border-neutral-200 dark:border-neutral-700 rounded-lg">
                    <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                        <thead class="bg-neutral-100 dark:bg-neutral-900">
                            <tr class="divide-x divide-neutral-200 dark:divide-neutral-700">
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">
                                    Nama Unit
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">
                                    Lokasi
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                            @forelse ($unitKerjas as $unit)
                                <tr class="divide-x divide-neutral-200 dark:divide-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $unit->nama_unit }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $unit->lokasi }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center space-x-3">
                                            <a href="{{ route('admin.unit-kerja.edit', $unit) }}" class="px-3 py-1.5 text-xs font-medium text-white bg-neutral-600 rounded-md hover:bg-neutral-700 transition">Edit</a>
                                            <form action="{{ route('admin.unit-kerja.destroy', $unit) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center text-sm text-neutral-500">
                                        Belum ada data Unit Kerja.
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