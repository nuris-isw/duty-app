<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-900 dark:text-neutral-100 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Flash Message --}}
            @if (session('success'))
                <div class="p-4 rounded-xl bg-green-50 border-l-4 border-green-400 text-green-700 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($currentlyOnLeave->isNotEmpty())
                <div class="bg-blue-50 dark:bg-blue-900/50 border-l-4 border-blue-400 p-4 rounded-xl">
                    <h4 class="font-semibold text-blue-800 dark:text-blue-200">Rekan Kerja yang Cuti/Izin Hari Ini</h4>
                    <ul class="list-disc list-inside mt-2 text-sm text-blue-700 dark:text-blue-300">
                        @foreach ($currentlyOnLeave as $leave)
                            <li>{{ $leave->user->name }} ({{ $leave->leave_type }})</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (Auth::user()->role === 'admin')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-stat-card title="Total Pegawai" value="{{ $stats['total_pegawai'] }}" iconBgColor="bg-brand">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </x-stat-card>
                    <x-stat-card title="Pengajuan Pending" value="{{ $stats['pending_requests'] }}" iconBgColor="bg-yellow-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </x-stat-card>
                    <x-stat-card title="Disetujui Bulan Ini" value="{{ $stats['approved_this_month'] }}" iconBgColor="bg-green-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </x-stat-card>
                </div>

                <div class="bg-white dark:bg-neutral-800 shadow-md rounded-2xl p-6 overflow-hidden">
                    <h3 class="font-semibold text-lg text-brand-dark mb-4">Daftar Pegawai Cuti (30 Hari ke Depan)</h3>
                    <div class="overflow-x-auto border border-neutral-200 dark:border-neutral-700 rounded-lg">
                        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                            <thead class="bg-neutral-100 dark:bg-neutral-900">
                                <tr class="divide-x divide-neutral-200 dark:divide-neutral-700">
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Nama Pegawai</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Jabatan</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Tanggal Cuti</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                                @forelse ($upcomingLeaves as $leave)
                                    <tr class="divide-x divide-neutral-200 dark:divide-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $leave->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $leave->user->jabatan->nama_jabatan ?? 'N/A' }} {{ $leave->user->jabatan->bidang_kerja ?? '' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">{{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-10 text-center text-neutral-500">Tidak ada pegawai yang cuti.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if (Auth::user()->role === 'atasan' && $subordinatePendingRequests->isNotEmpty())
                <div class="bg-white dark:bg-neutral-800 shadow-md rounded-2xl p-6 overflow-hidden">
                    <h3 class="font-semibold text-lg text-brand-dark mb-4">Pengajuan Cuti Perlu Persetujuan</h3>
                    <div class="overflow-x-auto border border-neutral-200 dark:border-neutral-700 rounded-lg">
                        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                            <thead class="bg-neutral-100 dark:bg-neutral-900">
                                <tr class="divide-x divide-neutral-200 dark:divide-neutral-700">
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Nama Pemohon</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Jenis Cuti</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Alasan</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Tanggal</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Dokumen</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                                @foreach ($subordinatePendingRequests as $request)
                                    <tr class="divide-x divide-neutral-200 dark:divide-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $request->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">{{ $request->leave_type }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">{{ $request->reason }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">{{ \Carbon\Carbon::parse($request->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if ($request->dokumen_pendukung)
                                                <a href="{{ asset('storage/' . $request->dokumen_pendukung) }}" target="_blank" class="text-blue-600 hover:underline">
                                                    Lihat
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center space-x-2">
                                                <form action="{{ route('leave-requests.approve', $request) }}" method="POST">@csrf @method('PATCH')<x-primary-button class="!py-1.5 !px-3 !text-xs">{{ __('Setujui') }}</x-primary-button></form>
                                                <form 
                                                    x-data="{ reason: '' }"
                                                    x-on:submit.prevent="
                                                        reason = prompt('Silakan masukkan alasan penolakan:');
                                                        if (reason) {
                                                            $refs.reasonInput.value = reason;
                                                            $el.submit();
                                                        }
                                                    "
                                                    action="{{ route('leave-requests.reject', $request) }}" 
                                                    method="POST"
                                                >
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="rejection_reason" x-ref="reasonInput">
                                                    <x-danger-button type="submit" class="!py-1.5 !px-3 !text-xs">{{ __('Tolak') }}</x-danger-button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            
            <div class="bg-white dark:bg-neutral-800 shadow-md rounded-2xl p-6 overflow-hidden">
                <div class="flex flex-wrap gap-4 justify-between items-center mb-4">
                    <h3 class="font-semibold text-lg text-brand-dark">Riwayat Pengajuan Cuti Anda</h3>
                    <a href="{{ route('leave-requests.create') }}"><x-primary-button type="button">+ Ajukan Cuti/Izin</x-primary-button></a>
                </div>
                <div class="overflow-x-auto border border-neutral-200 dark:border-neutral-700 rounded-lg">
                     <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                        <thead class="bg-neutral-100 dark:bg-neutral-900">
                            <tr class="divide-x divide-neutral-200 dark:divide-neutral-700">
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Jenis Cuti</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Alasan</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Tanggal</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                            @forelse ($myLeaveRequests as $request)
                                <tr class="divide-x divide-neutral-200 dark:divide-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $request->leave_type }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $request->reason }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">{{ \Carbon\Carbon::parse($request->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @php $statusClass = ['pending' => 'bg-yellow-100 text-yellow-800', 'approved' => 'bg-green-100 text-green-800', 'rejected' => 'bg-red-100 text-red-800'][$request->status] ?? 'bg-gray-100 text-gray-800'; @endphp
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">{{ ucfirst($request->status) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($request->status === 'approved')
                                            <a href="{{ route('leave-requests.print', $request) }}" class="px-3 py-1.5 rounded-lg bg-neutral-600 text-white text-xs hover:bg-neutral-900 transition" target="_blank">Cetak</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-10 text-center text-neutral-500">Anda belum memiliki riwayat pengajuan cuti.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if (Auth::user()->role === 'atasan' && $subordinateHistoryRequests->isNotEmpty())
                <div class="bg-white dark:bg-neutral-800 shadow-md rounded-2xl p-6 overflow-hidden">
                    <h3 class="font-semibold text-lg text-neutral-800 dark:text-neutral-100 mb-4">Riwayat Pengajuan Pegawai</h3>
                    <div class="overflow-x-auto border border-neutral-200 dark:border-neutral-700 rounded-lg">
                        <table class="min-w-full text-sm">
                            <thead class="bg-neutral-100 dark:bg-neutral-900">
                                <tr class="divide-x divide-neutral-200 dark:divide-neutral-700">
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Nama Pemohon</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Jenis Cuti</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Alasan</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Tanggal</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Dokumen</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-600 dark:text-neutral-300 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-neutral-200 dark:divide-neutral-700">
                                @foreach ($subordinateHistoryRequests as $request)
                                    <tr class="divide-x divide-neutral-200 dark:divide-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $request->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $request->leave_type }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $request->reason }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($request->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if ($request->dokumen_pendukung)
                                                <a href="{{ asset('storage/' . $request->dokumen_pendukung) }}" target="_blank" class="text-blue-600 hover:underline">
                                                    Lihat
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php 
                                                $statusClass = [
                                                    'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300', 
                                                    'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300'
                                                ][$request->status] ?? 'bg-gray-100 text-gray-800'; 
                                            @endphp
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">{{ ucfirst($request->status) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
    function handleRejection(form) {
        const reason = prompt('Silakan masukkan alasan penolakan:');
        
        // Jika user mengisi alasan dan menekan OK
        if (reason) {
            form.querySelector('input[name="rejection_reason"]').value = reason;
            return true; // Lanjutkan submit form
        }
        
        // Jika user menekan Cancel atau tidak mengisi apa-apa
        return false; // Batalkan submit form
    }
</script>
@endpush