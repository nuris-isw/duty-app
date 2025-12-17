<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-800 dark:text-neutral-200 leading-tight">
            {{ __('Riwayat Absensi Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-neutral-800 shadow-md rounded-2xl p-6">
                
                {{-- Filter Tanggal --}}
                <form method="GET" action="{{ route('my-attendance.index') }}" class="mb-8 flex flex-wrap gap-4 items-end justify-between border-b border-neutral-100 dark:border-neutral-700 pb-6">
                    <div class="flex gap-4">
                        <div>
                            <x-input-label for="start_date" :value="__('Dari')" />
                            <x-text-input id="start_date" class="block mt-1 text-sm" type="date" name="start_date" :value="$startDate" />
                        </div>
                        <div>
                            <x-input-label for="end_date" :value="__('Sampai')" />
                            <x-text-input id="end_date" class="block mt-1 text-sm" type="date" name="end_date" :value="$endDate" />
                        </div>
                    </div>
                    <x-primary-button>{{ __('Filter Data') }}</x-primary-button>
                </form>

                {{-- Header Tabel (Opsional, agar lebih jelas) --}}
                <div class="hidden md:grid grid-cols-3 mb-4 px-4 text-xs font-bold text-neutral-500 uppercase tracking-wider">
                    <div class="text-left">Tanggal</div>
                    <div class="text-center">Waktu Absen</div>
                    <div class="text-right">Keterangan</div>
                </div>

                {{-- List Absensi --}}
                <div class="space-y-3">
                    @forelse($attendanceHistory as $row)
                        <div class="grid grid-cols-3 items-center p-4 border border-neutral-200 dark:border-neutral-700 rounded-xl hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition duration-150">
                            
                            {{-- KOLOM 1: TANGGAL (Rata Kiri) --}}
                            <div class="text-left flex flex-col">
                                <span class="font-bold text-neutral-800 dark:text-neutral-200 text-sm md:text-base">
                                    {{ \Carbon\Carbon::parse($row['date'])->format('d M Y') }}
                                </span>
                                <span class="text-xs text-neutral-500 font-medium">
                                    {{ $row['day_name'] }}
                                </span>
                            </div>

                            {{-- KOLOM 2: WAKTU ABSEN (Rata Tengah) --}}
                            <div class="text-center flex flex-col md:flex-row items-center justify-center gap-2">
                                {{-- Jam Masuk (Hijau) --}}
                                <div class="flex flex-col items-center">
                                    <span class="text-[10px] text-neutral-400 uppercase leading-none mb-1 md:hidden">Masuk</span>
                                    <span class="px-2.5 py-1 text-xs font-bold text-green-700 bg-green-100 border border-green-200 rounded-md dark:bg-green-900/30 dark:text-green-400 dark:border-green-800">
                                        {{ $row['clock_in'] }}
                                    </span>
                                </div>

                                <span class="hidden md:block text-neutral-300">-</span>

                                {{-- Jam Pulang (Oranye) --}}
                                <div class="flex flex-col items-center">
                                    <span class="text-[10px] text-neutral-400 uppercase leading-none mb-1 md:hidden">Pulang</span>
                                    <span class="px-2.5 py-1 text-xs font-bold text-orange-700 bg-orange-100 border border-orange-200 rounded-md dark:bg-orange-900/30 dark:text-orange-400 dark:border-orange-800">
                                        {{ $row['clock_out'] }}
                                    </span>
                                </div>
                            </div>

                            {{-- KOLOM 3: STATUS (Rata Kanan) --}}
                            <div class="text-right">
                                <span class="text-sm font-medium {{ $row['color_class'] }}">
                                    {{ $row['status'] }}
                                </span>
                            </div>

                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 border-2 border-dashed border-neutral-200 dark:border-neutral-700 rounded-xl text-neutral-400">
                            <svg class="w-12 h-12 mb-3 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm">Tidak ada data absensi pada periode ini.</span>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>