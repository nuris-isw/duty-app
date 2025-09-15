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
                    <div class="flex flex-col md:flex-row md:items-end gap-4">
                        <div class="flex-1">
                            <x-input-label for="start_date" :value="__('Dari Tanggal')" />
                            <x-text-input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="mt-1 block w-full" />
                        </div>
                        <div class="flex-1">
                            <x-input-label for="end_date" :value="__('Sampai Tanggal')" />
                            <x-text-input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="mt-1 block w-full" />
                        </div>
                        <div class="flex items-center gap-2">
                            <x-primary-button type="submit">Filter</x-primary-button>
                            <a href="{{ route('admin.reports.print', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank">
                                <x-secondary-button type="button">Cetak PDF</x-secondary-button>
                            </a>
                        </div>
                    </div>
                </form>

                {{-- Tabel Data dengan Gaya Grid --}}
                <div class="overflow-x-auto border border-neutral-200 dark:border-neutral-700 rounded-lg">
                    <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                        <thead class="bg-neutral-100 dark:bg-neutral-900">
                            <tr class="divide-x divide-neutral-200 dark:divide-neutral-700">
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Nama Pegawai</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Jabatan</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Jenis Cuti</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Tanggal</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                            @forelse ($leaveRequests as $request)
                                <tr class="divide-x divide-neutral-200 dark:divide-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $request->user->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $request->user->jabatan->nama_jabatan ?? 'N/A' }} {{ $request->user->jabatan->alias ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $request->leave_type }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">{{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $statusClass = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'approved' => 'bg-green-100 text-green-800',
                                                'rejected' => 'bg-red-100 text-red-800',
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
                                        Tidak ada data untuk ditampilkan pada rentang tanggal ini.
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