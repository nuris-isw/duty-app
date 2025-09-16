<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-900 dark:text-neutral-100 leading-tight">
            {{ __('Laporan Pengajuan Izin & Cuti') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                            @forelse ($leaveRequests as $request)
                                <tr class="divide-x divide-neutral-200 dark:divide-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $request->user->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $request->user->jabatan->nama_jabatan ?? 'N/A' }} {{ $request->user->jabatan->alias ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $request->leave_type }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $statusClass = [
                                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300',
                                                'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
                                                'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
                                            ][$request->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-neutral-500">
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