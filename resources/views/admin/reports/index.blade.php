<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-brand-dark leading-tight">
            {{ __('Laporan Pengajuan Izin & Cuti') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-2xl p-6">
                
                <form method="GET" action="{{ route('admin.reports.index') }}" class="mb-6">
                    <div class="flex items-center space-x-4">
                        <div>
                            <label for="start_date" class="text-sm font-medium text-gray-700">Dari Tanggal</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="end_date" class="text-sm font-medium text-gray-700">Sampai Tanggal</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="pt-6">
                            <button type="submit" class="px-4 py-2 rounded-lg bg-brand-accent text-black hover:bg-red-700">
                                Filter
                            </button>
                            <a href="{{ route('admin.reports.print', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                                target="_blank"
                                class="px-4 py-2 rounded-lg bg-gray-600 text-black hover:bg-gray-700">
                                    Cetak PDF
                            </a>
                        </div>
                    </div>
                </form>

                <div class="overflow-x-auto rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300 text-sm">
                        <thead class="bg-brand-dark text-black">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium">Nama Pegawai</th>
                                <th class="px-6 py-3 text-left font-medium">Jabatan</th>
                                <th class="px-6 py-3 text-left font-medium">Jenis Cuti</th>
                                <th class="px-6 py-3 text-left font-medium">Tanggal</th>
                                <th class="px-6 py-3 text-left font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($leaveRequests as $request)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">{{ $request->user->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ $request->user->jabatan->nama_jabatan ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ $request->leave_type }}</td>
                                    <td class="px-6 py-4">
                                        {{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }} - 
                                        {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusClass = [
                                                'pending' => 'bg-yellow-200 text-yellow-900',
                                                'approved' => 'bg-green-200 text-green-900',
                                                'rejected' => 'bg-red-200 text-red-900',
                                            ][$request->status] ?? 'bg-gray-200 text-gray-800';
                                        @endphp
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        Tidak ada data untuk ditampilkan.
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