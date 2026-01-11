<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-900 dark:text-neutral-100 leading-tight">
            {{ __('Manajemen User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-neutral-800 shadow-md rounded-2xl p-6 overflow-hidden">
                
                {{-- Notifikasi --}}
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 border-l-4 border-green-400 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 border-l-4 border-red-400 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Tombol Tambah User --}}
                <div class="mb-6">
                    <x-primary-button onclick="window.location='{{ route('admin.users.create') }}'">
                        + Tambah User Baru
                    </x-primary-button>
                </div>

                {{-- Tabel User dengan Grid --}}
                <div class="overflow-x-auto border border-neutral-200 dark:border-neutral-700 rounded-lg">
                    <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                        <thead class="bg-neutral-100 dark:bg-neutral-900">
                            <tr class="divide-x divide-neutral-200 dark:divide-neutral-700">
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">
                                    Nama & Email
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">
                                    Role
                                </th>
                                {{-- KOLOM BARU: Unit Kerja --}}
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">
                                    Unit Kerja
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
                            @forelse ($users as $user)
                                <tr class="divide-x divide-neutral-200 dark:divide-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition">
                                    
                                    {{-- Kolom Nama & Email (Digabung agar ringkas) --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $user->name }}</div>
                                        <div class="text-xs text-neutral-500 dark:text-neutral-400">{{ $user->email }}</div>
                                    </td>

                                    {{-- Kolom Role (Update Warna Badge) --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $roleClass = '';
                                            switch($user->role) {
                                                case 'superadmin': 
                                                    $roleClass = 'bg-purple-100 text-purple-800 border border-purple-200'; break;
                                                case 'sys_admin': 
                                                    $roleClass = 'bg-rose-100 text-rose-800 border border-rose-200'; break;
                                                case 'unit_admin': 
                                                    $roleClass = 'bg-blue-100 text-blue-800 border border-blue-200'; break;
                                                default: // user
                                                    $roleClass = 'bg-gray-100 text-gray-800 border border-gray-200'; 
                                            }
                                        @endphp
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $roleClass }}">
                                            {{-- Ubah format teks (superadmin -> Superadmin) --}}
                                            {{ ucwords(str_replace('_', ' ', $user->role)) }}
                                        </span>
                                    </td>

                                    {{-- Kolom Unit Kerja --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-neutral-600 dark:text-neutral-300">
                                        {{ $user->unitKerja->nama_unit ?? '-' }}
                                    </td>

                                    {{-- Kolom Lokasi --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-neutral-600 dark:text-neutral-300">
                                        {{ $user->unitKerja->lokasi ?? '-' }}
                                    </td>
                        
                                    {{-- Kolom Aksi --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            
                                            {{-- Tombol Edit (Semua Admin Master Data boleh) --}}
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="px-3 py-1.5 text-xs font-medium text-white bg-neutral-600 rounded-md hover:bg-neutral-700 transition">
                                                Edit
                                            </a>

                                            {{-- Tombol Hapus (HANYA SUPERADMIN) --}}
                                            {{-- Menggunakan Gate 'manage-superadmin' atau cek manual role --}}
                                            @if(auth()->user()->role === 'superadmin') 
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-neutral-500">
                                        Belum ada data user.
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