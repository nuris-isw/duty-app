<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-800 dark:text-neutral-200 leading-tight">
            {{ __('Riwayat Absensi Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8"> {{-- Diperlebar jadi max-w-5xl --}}
            <div class="bg-white dark:bg-neutral-800 shadow-md rounded-2xl p-6 border border-neutral-100 dark:border-neutral-700">
                
                {{-- Filter & Tombol Buat Baru --}}
                <div class="flex flex-col md:flex-row justify-between items-end gap-4 mb-8 border-b border-neutral-100 dark:border-neutral-700 pb-6">
                    <form method="GET" action="{{ route('my-attendance.index') }}" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <x-input-label for="start_date" :value="__('Dari')" />
                            <x-text-input id="start_date" class="block mt-1 text-sm" type="date" name="start_date" :value="$startDate" />
                        </div>
                        <div>
                            <x-input-label for="end_date" :value="__('Sampai')" />
                            <x-text-input id="end_date" class="block mt-1 text-sm" type="date" name="end_date" :value="$endDate" />
                        </div>
                        <x-primary-button>{{ __('Filter') }}</x-primary-button>
                    </form>

                    {{-- Group Tombol Aksi --}}
                    <div class="flex gap-2 w-full md:w-auto justify-end">
                        
                        {{-- TOMBOL BARU: Lihat Daftar Pengajuan --}}
                        <a href="{{ route('attendance-correction.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 rounded-lg font-semibold text-xs text-neutral-700 dark:text-neutral-300 uppercase tracking-widest shadow-sm hover:bg-neutral-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Riwayat Pengajuan
                        </a>

                        {{-- Tombol Buat Baru --}}
                        <a href="{{ route('attendance-correction.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Ajukan Koreksi
                        </a>
                    </div>
                </div>

                {{-- 2. AREA SUMMARY CARDS (UPDATE 5 KOLOM) --}}
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
                    {{-- Card Hadir --}}
                    <div class="p-4 rounded-xl border border-green-100 bg-green-50 dark:border-green-800 dark:bg-green-900/20 flex flex-col items-center">
                        <span class="text-2xl font-bold text-green-700 dark:text-green-400">{{ $summary['hadir'] }}</span>
                        <span class="text-xs font-medium text-green-600 dark:text-green-300 uppercase mt-1">Kehadiran</span>
                    </div>

                    {{-- Card Terlambat --}}
                    <div class="p-4 rounded-xl border border-yellow-100 bg-yellow-50 dark:border-yellow-800 dark:bg-yellow-900/20 flex flex-col items-center">
                        <span class="text-2xl font-bold text-yellow-700 dark:text-yellow-400">{{ $summary['terlambat'] }}</span>
                        <span class="text-xs font-medium text-yellow-600 dark:text-yellow-300 uppercase mt-1">Terlambat</span>
                    </div>

                    {{-- Card Data Tidak Lengkap (BARU) --}}
                    <div class="p-4 rounded-xl border border-purple-100 bg-purple-50 dark:border-purple-800 dark:bg-purple-900/20 flex flex-col items-center">
                        <span class="text-2xl font-bold text-purple-700 dark:text-purple-400">{{ $summary['data_tidak_lengkap'] }}</span>
                        <span class="text-xs font-medium text-purple-600 dark:text-purple-300 uppercase mt-1 text-center">Data Tdk Lengkap</span>
                    </div>

                    {{-- Card Mangkir --}}
                    <div class="p-4 rounded-xl border border-red-100 bg-red-50 dark:border-red-800 dark:bg-red-900/20 flex flex-col items-center">
                        <span class="text-2xl font-bold text-red-700 dark:text-red-400">{{ $summary['mangkir'] }}</span>
                        <span class="text-xs font-medium text-red-600 dark:text-red-300 uppercase mt-1">Tanpa Ket.</span>
                    </div>

                    {{-- Card Cuti/Izin/Sakit --}}
                    <div class="p-4 rounded-xl border border-blue-100 bg-blue-50 dark:border-blue-800 dark:bg-blue-900/20 flex flex-col items-center">
                        <span class="text-2xl font-bold text-blue-700 dark:text-blue-400">{{ $summary['izin_cuti'] }}</span>
                        <span class="text-xs font-medium text-blue-600 dark:text-blue-300 uppercase mt-1 text-center">Cuti / Izin / Sakit</span>
                    </div>
                </div>

                {{-- Header Grid (5 KOLOM) --}}
                <div class="hidden md:grid grid-cols-5 mb-4 px-4 text-xs font-bold text-neutral-500 uppercase tracking-wider">
                    <div class="text-left">Tanggal</div>
                    <div class="text-center">Jam Datang</div>
                    <div class="text-center">Jam Pulang</div>
                    <div class="text-center">Status</div>
                    <div class="text-right">Aksi</div> {{-- Kolom Baru --}}
                </div>

                {{-- List Absensi --}}
                <div class="space-y-3">
                    @forelse($attendanceHistory as $row)
                        {{-- Grid Cols 5 --}}
                        <div class="grid grid-cols-1 md:grid-cols-5 items-center p-4 border border-neutral-200 dark:border-neutral-700 rounded-xl hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition duration-150 gap-2 md:gap-0">
                            
                            {{-- 1. TANGGAL --}}
                            <div class="flex flex-row justify-between md:flex-col md:text-left">
                                <span class="font-bold text-neutral-800 dark:text-neutral-200 text-sm md:text-base">
                                    {{ \Carbon\Carbon::parse($row['date'])->format('d M Y') }}
                                </span>
                                <span class="text-xs text-neutral-500 font-medium md:mt-1">
                                    {{ $row['day_name'] }}
                                </span>
                            </div>

                            {{-- 2. JAM DATANG --}}
                            <div class="flex flex-row justify-between md:flex-col items-center md:justify-center">
                                <span class="text-xs text-neutral-400 font-bold md:hidden">Datang:</span>
                                @if($row['clock_in'] !== '-')
                                    <span class="px-2.5 py-1 text-xs font-mono font-bold text-green-700 bg-green-100 border border-green-200 rounded-md">
                                        {{ $row['clock_in'] }}
                                    </span>
                                @else
                                    <span class="text-xs text-neutral-400 italic">-</span>
                                @endif
                            </div>

                            {{-- 3. JAM PULANG --}}
                            <div class="flex flex-row justify-between md:flex-col items-center md:justify-center">
                                <span class="text-xs text-neutral-400 font-bold md:hidden">Pulang:</span>
                                @if($row['clock_out'] !== '-')
                                    <span class="px-2.5 py-1 text-xs font-mono font-bold text-orange-700 bg-orange-100 border border-orange-200 rounded-md">
                                        {{ $row['clock_out'] }}
                                    </span>
                                @else
                                    <span class="text-xs text-neutral-400 italic">-</span>
                                @endif
                            </div>

                            {{-- 4. STATUS --}}
                            <div class="flex justify-between md:justify-center items-center mt-2 md:mt-0">
                                <span class="text-xs text-neutral-400 font-bold md:hidden">Status:</span>
                                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $row['color_class'] }} text-center min-w-[80px]">
                                    {{ $row['status'] }}
                                </span>
                            </div>

                            {{-- 5. AKSI (Tombol Koreksi) --}}
                            <div class="flex justify-end mt-2 md:mt-0">
                                @php
                                    $status = $row['status'];
                                    $rowDate = $row['date']; // Tanggal pada baris ini
                                    $today = date('Y-m-d');  // Tanggal hari ini

                                    // 1. Filter Status: Sembunyikan jika sudah Hadir/Libur/Cuti/Sakit/Izin
                                    $hideKeywords = ['Hadir', 'Libur', 'Cuti', 'Sakit', 'Izin'];
                                    $isStatusFinal = \Illuminate\Support\Str::contains($status, $hideKeywords, true);
                                    
                                    // 2. Filter Waktu: Sembunyikan jika tanggal adalah Masa Depan (Besok dst)
                                    $isFutureDate = $rowDate > $today;
                                @endphp

                                {{-- Tampilkan tombol HANYA JIKA: Status belum final DAN Bukan masa depan --}}
                                @if(!$isStatusFinal && !$isFutureDate) 
                                    <a href="{{ route('attendance-correction.create', ['date' => $row['date']]) }}" 
                                    class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 rounded-lg text-xs font-medium text-neutral-700 dark:text-neutral-300 shadow-sm hover:bg-neutral-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5 text-neutral-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                        Koreksi
                                    </a>
                                @endif
                            </div>

                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 border-2 border-dashed border-neutral-200 dark:border-neutral-700 rounded-xl text-neutral-400">
                            <span class="text-sm italic">Tidak ada data absensi.</span>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>