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
                <div x-data="{ show: true }" x-show="show" class="p-4 rounded-xl bg-green-50 dark:bg-green-900/30 border-l-4 border-green-400 text-green-700 dark:text-green-300 shadow-sm flex justify-between items-center">
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="text-green-700 dark:text-green-300 hover:text-green-900 font-bold">&times;</button>
                </div>
            @endif

            {{-- ========================================================== --}}
            {{-- AREA ADMIN & MANAGER (Superadmin, SysAdmin, Unit Admin) --}}
            {{-- ========================================================== --}}
            @can('access-admin-panel')
                
                {{-- 1. Info Pegawai Cuti Hari Ini --}}
                @if(isset($currentlyOnLeave) && $currentlyOnLeave->isNotEmpty())
                    <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-4 rounded-xl">
                        <h4 class="font-semibold text-blue-800 dark:text-blue-200 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                            Sedang Cuti / Izin Hari Ini
                        </h4>
                        <ul class="list-disc list-inside mt-2 text-sm text-blue-700 dark:text-blue-300 ml-1">
                            @foreach ($currentlyOnLeave as $leave)
                                <li>
                                    <span class="font-bold">{{ $leave->user->name }}</span> 
                                    <span class="text-xs opacity-80">({{ $leave->leave_type }})</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- 2. Statistik Ringkas --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-stat-card title="Total Pegawai" value="{{ $stats['total_pegawai'] ?? 0 }}" iconBgColor="bg-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </x-stat-card>
                    <x-stat-card title="Pengajuan Pending" value="{{ $stats['pending_requests'] ?? 0 }}" iconBgColor="bg-amber-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </x-stat-card>
                    <x-stat-card title="Disetujui Bulan Ini" value="{{ $stats['approved_this_month'] ?? 0 }}" iconBgColor="bg-emerald-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </x-stat-card>
                </div>

                {{-- 3. Tabel Cuti Akan Datang --}}
                <div class="bg-white dark:bg-neutral-800 shadow-sm rounded-2xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800">
                        <h3 class="font-semibold text-lg text-neutral-800 dark:text-neutral-200">Jadwal Cuti Pegawai (30 Hari ke Depan)</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                            <thead class="bg-neutral-50 dark:bg-neutral-900">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Nama Pegawai</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Jabatan / Unit</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700 bg-white dark:bg-neutral-800">
                                @forelse ($upcomingLeaves as $leave)
                                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $leave->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                            {{ $leave->user->jabatan->nama_jabatan ?? '-' }}
                                            <span class="text-xs text-neutral-400 block">{{ $leave->user->unitKerja->nama_unit ?? '' }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-neutral-600 dark:text-neutral-300">
                                            {{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-8 text-center text-sm text-neutral-500 dark:text-neutral-400">Tidak ada jadwal cuti dalam waktu dekat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ========================================================== --}}
                {{-- SECTION BARU: MONITORING SISA CUTI --}}
                {{-- ========================================================== --}}
                <div class="mt-8 bg-white dark:bg-neutral-800 shadow-sm rounded-2xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                    <div class="px-6 py-5 border-b border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <h3 class="font-bold text-lg text-neutral-800 dark:text-neutral-200">
                            Monitoring Sisa Cuti Tahunan
                            @if(auth()->user()->role == 'unit_admin')
                                <span class="text-sm font-normal text-neutral-500 ml-2">(Unit: {{ auth()->user()->unitKerja->nama_unit ?? 'Saya' }})</span>
                            @else
                                <span class="text-sm font-normal text-neutral-500 ml-2">(Seluruh Pegawai)</span>
                            @endif
                        </h3>
                    </div>
                    
                    {{-- Tabel Full Height (Tanpa Scroll Internal) --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                            <thead class="bg-neutral-100 dark:bg-neutral-900">
                                <tr>
                                    {{-- Kolom No --}}
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-bold text-neutral-500 uppercase tracking-wider w-10">No</th>
                                    
                                    {{-- Kolom Unit Kerja (Digeser ke depan agar terlihat pengelompokannya) --}}
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Unit Kerja</th>

                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider w-1/3">Nama Pegawai</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-500 uppercase tracking-wider">Terpakai</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-500 uppercase tracking-wider">Sisa Cuti</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700 bg-white dark:bg-neutral-800">
                                @forelse ($employeeBalances as $emp)
                                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition duration-150">
                                        {{-- No --}}
                                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-neutral-400">
                                            {{ $loop->iteration }}
                                        </td>

                                        {{-- Unit Kerja (Sekarang jadi patokan urutan) --}}
                                        <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-neutral-600 dark:text-neutral-300">
                                            {{ $emp->unitKerja->nama_unit ?? '-' }}
                                        </td>

                                        {{-- Nama --}}
                                        <td class="px-6 py-3 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ $emp->name }}</div>
                                        </td>
                                        
                                        {{-- Terpakai --}}
                                        <td class="px-6 py-3 whitespace-nowrap text-center text-sm text-neutral-500">
                                            {{ $emp->kuota_terpakai }}
                                        </td>
                                        
                                        {{-- Sisa Cuti --}}
                                        <td class="px-6 py-3 whitespace-nowrap text-center">
                                            @php
                                                $bgClass = 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800'; 
                                                
                                                if ($emp->sisa_cuti <= 3) {
                                                    $bgClass = 'bg-red-100 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800'; 
                                                } elseif ($emp->sisa_cuti <= 5) {
                                                    $bgClass = 'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800'; 
                                                }
                                            @endphp
                                            <span class="inline-flex items-center justify-center w-12 py-1 text-sm font-bold rounded-md border {{ $bgClass }}">
                                                {{ $emp->sisa_cuti }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-sm text-neutral-500">Data pegawai tidak ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Footer Info (Optional) --}}
                    @if($employeeBalances->count() > 30)
                    <div class="px-6 py-3 bg-neutral-50 dark:bg-neutral-800 border-t border-neutral-200 dark:border-neutral-700 text-xs text-neutral-500 text-center">
                        Menampilkan seluruh {{ $employeeBalances->count() }} pegawai
                    </div>
                    @endif
                </div>
            @endcan

            {{-- ========================================================== --}}
            {{-- AREA APPROVAL (Muncul Jika Ada Request dari Bawahan) --}}
            {{-- ========================================================== --}}
            @if(isset($subordinatePendingRequests) && $subordinatePendingRequests->isNotEmpty())
                <div class="bg-white dark:bg-neutral-800 shadow-lg rounded-2xl border border-neutral-200 dark:border-neutral-700 overflow-hidden ring-1 ring-orange-500/20">
                    <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 bg-orange-50 dark:bg-orange-900/10 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-orange-800 dark:text-orange-200 flex items-center gap-2">
                            <span class="flex h-3 w-3 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
                            </span>
                            Menunggu Persetujuan Anda
                        </h3>
                        <span class="bg-orange-100 text-orange-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-orange-200 dark:text-orange-900">
                            {{ $subordinatePendingRequests->count() }} Permintaan
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                            <thead class="bg-neutral-50 dark:bg-neutral-900">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Pemohon</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-500 uppercase tracking-wider">Detail Cuti</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700 bg-white dark:bg-neutral-800">
                                @foreach ($subordinatePendingRequests as $request)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-neutral-900 dark:text-white">{{ $request->user->name }}</div>
                                            <div class="text-xs text-neutral-500">{{ $request->leave_type }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-neutral-500 dark:text-neutral-300">
                                            <div class="text-center font-semibold">{{ \Carbon\Carbon::parse($request->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('d M') }}</div>
                                            <div class="text-center text-xs italic mt-1">"{{Str::limit($request->reason, 30)}}"</div>
                                            @if ($request->dokumen_pendukung)
                                                <div class="text-center mt-1">
                                                    <a href="{{ asset('storage/' . $request->dokumen_pendukung) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs underline">Lihat Dokumen</a>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                {{-- Tombol Setujui --}}
                                                <form action="{{ route('leave-requests.approve', $request) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md transition shadow-sm">
                                                        Setujui
                                                    </button>
                                                </form>

                                                {{-- Tombol Tolak (AlpineJS) --}}
                                                <form action="{{ route('leave-requests.reject', $request) }}" method="POST"
                                                      x-data
                                                      @submit.prevent="
                                                          const reason = prompt('Alasan penolakan:');
                                                          if (reason) {
                                                              $el.querySelector('[name=rejection_reason]').value = reason;
                                                              $el.submit();
                                                          }
                                                      ">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="rejection_reason">
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-md transition shadow-sm">
                                                        Tolak
                                                    </button>
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
            
            {{-- ========================================================== --}}
            {{-- AREA PERSONAL (Semua User) --}}
            {{-- ========================================================== --}}
            <div class="bg-white dark:bg-neutral-800 shadow-sm rounded-2xl border border-neutral-200 dark:border-neutral-700 p-6">
                <div class="flex flex-wrap gap-4 justify-between items-center mb-6">
                    <h3 class="font-semibold text-lg text-neutral-800 dark:text-neutral-200">Riwayat Pengajuan Saya</h3>
                    <a href="{{ route('leave-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        + Ajukan Cuti Baru
                    </a>
                </div>
                <div class="overflow-x-auto rounded-lg border border-neutral-100 dark:border-neutral-700">
                    <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                        <thead class="bg-neutral-50 dark:bg-neutral-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Jenis Cuti</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-neutral-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-neutral-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700 bg-white dark:bg-neutral-800">
                            @forelse ($myLeaveRequests as $request)
                                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900 dark:text-neutral-100">
                                        {{ $request->leave_type }}
                                        <div class="text-xs text-neutral-400 mt-0.5 truncate max-w-[200px]">{{ $request->reason }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-600 dark:text-neutral-300">
                                        {{ \Carbon\Carbon::parse($request->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php 
                                            $badges = [
                                                'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                'rejected' => 'bg-rose-100 text-rose-800 border-rose-200'
                                            ];
                                            $badgeClass = $badges[$request->status] ?? 'bg-gray-100 text-gray-800'; 
                                        @endphp
                                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full border {{ $badgeClass }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if ($request->status === 'approved')
                                            <a href="{{ route('leave-requests.print', $request) }}" target="_blank" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-white transition" title="Cetak Surat Izin">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                </svg>
                                            </a>
                                        @else
                                            <span class="text-neutral-300">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-8 text-center text-sm text-neutral-400">Belum ada riwayat pengajuan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 
                AREA RIWAYAT APPROVAL (Muncul Jika Ada Riwayat)
                Opsional: Biasanya atasan juga ingin lihat apa yang sudah mereka setujui/tolak
            --}}
            @if(isset($subordinateHistoryRequests) && $subordinateHistoryRequests->isNotEmpty())
                <div class="mt-8">
                    <h4 class="text-sm font-bold text-neutral-500 uppercase tracking-wide mb-3">Riwayat Persetujuan Terakhir</h4>
                    <div class="bg-white dark:bg-neutral-800 shadow-sm rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                        <table class="min-w-full text-sm">
                            <thead class="bg-neutral-50 dark:bg-neutral-900">
                                <tr>
                                    <th class="px-6 py-2 text-left text-xs font-medium text-neutral-500">Nama</th>
                                    <th class="px-6 py-2 text-left text-xs font-medium text-neutral-500">Tanggal Cuti</th>
                                    <th class="px-6 py-2 text-center text-xs font-medium text-neutral-500">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                                @foreach ($subordinateHistoryRequests as $history)
                                    <tr>
                                        <td class="px-6 py-3 text-neutral-700 dark:text-neutral-300">{{ $history->user->name }}</td>
                                        <td class="px-6 py-3 text-neutral-500">{{ \Carbon\Carbon::parse($history->start_date)->format('d/m/Y') }}</td>
                                        <td class="px-6 py-3 text-center">
                                            <span class="text-xs {{ $history->status == 'approved' ? 'text-green-600' : 'text-red-600' }}">
                                                {{ ucfirst($history->status) }}
                                            </span>
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