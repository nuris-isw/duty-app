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
                    <div class="mb-6 p-4 bg-green-50 text-green-700 border-l-4 border-green-400 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 text-red-700 border-l-4 border-red-400 rounded-lg">
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
                                    Nama
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">
                                    Email
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">
                                    Role
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">
                                    Sisa Cuti Tahunan
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                            @forelse ($users as $user)
                                <tr class="divide-x divide-neutral-200 dark:divide-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-neutral-900 dark:text-neutral-100">{{ $user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-neutral-600 dark:text-neutral-300">{{ $user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $roleClass = '';
                                            if ($user->role === 'admin') {
                                                $roleClass = 'bg-red-200 text-red-900';
                                            } elseif ($user->role === 'atasan') {
                                                $roleClass = 'bg-blue-200 text-blue-900';
                                            } else {
                                                $roleClass = 'bg-gray-200 text-gray-800';
                                            }
                                        @endphp

                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $roleClass }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        {{-- ▼▼▼ LOGIKA BARU UNTUK SISA CUTI ▼▼▼ --}}
                                        @php
                                            // Ambil record kuota spesifik user ini, jika ada.
                                            $quotaRecord = $user->userLeaveQuotas->first();
                                            
                                            // Jumlah yang diambil adalah dari record tersebut, atau 0 jika belum ada.
                                            $jumlahDiambil = $quotaRecord->jumlah_diambil ?? 0;
                                            
                                            // Sisa cuti adalah kuota default dikurangi yang sudah diambil.
                                            $sisaCuti = $annualLeaveQuota - $jumlahDiambil;
                                        @endphp
                                        
                                        {{-- Tampilkan 0 jika hasilnya negatif (untuk kasus anomali) --}}
                                        {{ max(0, $sisaCuti) }} hari
                                        {{-- ▲▲▲ SELESAI ▲▲▲ --}}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center space-x-3">
                                            <a href="{{ route('admin.leave-request.edit', $request->id) }}" class="px-3 py-1.5 text-xs font-medium text-white bg-neutral-600 rounded-md hover:bg-neutral-700 transition">Edit</a>
                                            <form action="{{ route('admin.leave-request.destroy', $request->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-sm text-neutral-500">
                                        Belum ada user.
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