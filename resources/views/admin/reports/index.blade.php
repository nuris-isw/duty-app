<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-900 dark:text-neutral-100 leading-tight">
            {{ __('Laporan Pengajuan Izin & Cuti') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 text-green-700 border-l-4 border-green-400 rounded-lg" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            <div class="bg-white dark:bg-neutral-800 shadow-md rounded-2xl p-6 sm:p-8 border border-neutral-200 dark:border-neutral-700">
                
                {{-- Filter Bar yang Responsif --}}
                <form method="GET" action="{{ route('admin.reports.index') }}" class="mb-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                        {{-- Filter Tanggal --}}
                        <div>
                            <x-input-label for="start_date" :value="__('Dari Tanggal')" />
                            <x-text-input type="date" name="start_date" id="start_date" value="{{ $filters['start_date'] ?? '' }}" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label for="end_date" :value="__('Sampai Tanggal')" />
                            <x-text-input type="date" name="end_date" id="end_date" value="{{ $filters['end_date'] ?? '' }}" class="mt-1 block w-full" />
                        </div>
                        
                        {{-- Filter Pegawai --}}
                        <div>
                            <x-input-label for="user_id" :value="__('Pegawai')" />
                            <x-select-input name="user_id" id="user_id" class="mt-1 block w-full">
                                <option value="">Semua Pegawai</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(($filters['user_id'] ?? '') == $user->id)>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </x-select-input>
                        </div>
                        
                        {{-- Filter Jenis Cuti --}}
                        <div>
                            <x-input-label for="leave_type" :value="__('Jenis Cuti')" />
                            <x-select-input name="leave_type" id="leave_type" class="mt-1 block w-full">
                                <option value="">Semua Jenis</option>
                                @foreach($leaveTypes as $type)
                                    <option value="{{ $type->nama_cuti }}" @selected(($filters['leave_type'] ?? '') == $type->nama_cuti)>
                                        {{ $type->nama_cuti }}
                                    </option>
                                @endforeach
                            </x-select-input>
                        </div>

                        {{-- Filter Status --}}
                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <x-select-input name="status" id="status" class="mt-1 block w-full">
                                <option value="">Semua Status</option>
                                <option value="pending" @selected(($filters['status'] ?? '') == 'pending')>Pending</option>
                                <option value="approved" @selected(($filters['status'] ?? '') == 'approved')>Approved</option>
                                <option value="rejected" @selected(($filters['status'] ?? '') == 'rejected')>Rejected</option>
                            </x-select-input>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <x-primary-button type="submit">Filter</x-primary-button>
                        <a href="{{ route('admin.reports.print', request()->query()) }}" target="_blank">
                            <x-secondary-button type="button">Cetak PDF</x-secondary-button>
                        </a>
                    </div>
                </form>

                {{-- Tabel Data dengan Gaya Grid --}}
                <div class="overflow-x-auto border border-neutral-200 dark:border-neutral-700 rounded-lg">
                    <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                        <thead class="bg-neutral-100 dark:bg-neutral-900">
                            <tr class="divide-x divide-neutral-200 dark:divide-neutral-700">
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Nama Pegawai</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Jabatan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Jenis Cuti</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Tanggal</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                            @forelse ($leaveRequests as $request)
                                <tr class="divide-x divide-neutral-200 dark:divide-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900 dark:text-neutral-100">{{ $request->user->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-600 dark:text-neutral-400">{{ $request->user->jabatan->alias ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-600 dark:text-neutral-400">{{ $request->leave_type }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-neutral-600 dark:text-neutral-400">
                                        {{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $statusClass = [
                                                'pending' => 'bg-amber-100 text-amber-800 border border-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-800',
                                                'approved' => 'bg-emerald-100 text-emerald-800 border border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800',
                                                'rejected' => 'bg-rose-100 text-rose-800 border border-rose-200 dark:bg-rose-900/30 dark:text-rose-300 dark:border-rose-800',
                                            ][$request->status] ?? 'bg-gray-100 text-gray-800 border border-gray-200';
                                        @endphp
                                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $statusClass }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center space-x-3">
                                            
                                            {{-- 1. Tombol Edit (Pensil) --}}
                                            {{-- Hanya tampil jika pending atau user punya hak edit --}}
                                            <a href="{{ route('admin.leave-requests.edit', $request->id) }}" 
                                            class="text-neutral-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition" 
                                            title="Edit Pengajuan">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>

                                            {{-- 2. Tombol Cetak PDF (Printer) --}}
                                            {{-- Hanya tampil jika status Approved --}}
                                            @if ($request->status === 'approved')
                                                <a href="{{ route('admin.leave-requests.print', $request->id) }}" 
                                                target="_blank"
                                                class="text-neutral-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition" 
                                                title="Cetak Surat Izin">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                    </svg>
                                                </a>
                                            @endif

                                            {{-- 3. Tombol Hapus (Tempat Sampah) --}}
                                            {{-- Hanya Superadmin/SysAdmin --}}
                                            @can('manage-master-data')
                                            <form action="{{ route('admin.leave-requests.destroy', $request->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini secara permanen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-neutral-500 hover:text-rose-600 dark:hover:text-rose-400 transition mt-1" 
                                                        title="Hapus Pengajuan">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                            @endcan

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-sm text-neutral-500 dark:text-neutral-400">
                                        Tidak ada data yang cocok dengan filter Anda.
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