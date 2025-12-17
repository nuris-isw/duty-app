<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-800 dark:text-neutral-200 leading-tight">
            {{ __('Riwayat Absensi Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-neutral-800 shadow-md rounded-2xl p-6 border border-neutral-100 dark:border-neutral-700">
                
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

                {{-- Header Grid (4 KOLOM) --}}
                <div class="hidden md:grid grid-cols-4 mb-4 px-4 text-xs font-bold text-neutral-500 uppercase tracking-wider">
                    <div class="text-left">Tanggal</div>
                    <div class="text-center">Jam Datang</div>
                    <div class="text-center">Jam Pulang</div>
                    <div class="text-right">Status</div>
                </div>

                {{-- List Absensi --}}
                <div class="space-y-3">
                    @forelse($attendanceHistory as $row)
                        {{-- Ubah menjadi grid-cols-4 --}}
                        <div class="grid grid-cols-1 md:grid-cols-4 items-center p-4 border border-neutral-200 dark:border-neutral-700 rounded-xl hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition duration-150 gap-2 md:gap-0">
                            
                            {{-- KOLOM 1: TANGGAL --}}
                            <div class="flex flex-row justify-between md:flex-col md:text-left">
                                <span class="font-bold text-neutral-800 dark:text-neutral-200 text-sm md:text-base">
                                    {{ \Carbon\Carbon::parse($row['date'])->format('d M Y') }}
                                </span>
                                <span class="text-xs text-neutral-500 font-medium md:mt-1">
                                    {{ $row['day_name'] }}
                                </span>
                            </div>

                            {{-- KOLOM 2: JAM DATANG (Hijau) --}}
                            <div class="flex flex-row justify-between md:flex-col items-center md:justify-center">
                                <span class="text-xs text-neutral-400 font-bold md:hidden">Datang:</span>
                                @if($row['clock_in'] !== '-')
                                    <span class="px-2.5 py-1 text-xs font-mono font-bold text-green-700 bg-green-100 border border-green-200 rounded-md">
                                        {{ $row['clock_in'] }}
                                    </span>
                                @else
                                    <span class="text-xs text-neutral-400 italic">Belum Absen</span>
                                @endif
                            </div>

                            {{-- KOLOM 3: JAM PULANG (Oranye) --}}
                            <div class="flex flex-row justify-between md:flex-col items-center md:justify-center">
                                <span class="text-xs text-neutral-400 font-bold md:hidden">Pulang:</span>
                                @if($row['clock_out'] !== '-')
                                    <span class="px-2.5 py-1 text-xs font-mono font-bold text-orange-700 bg-orange-100 border border-orange-200 rounded-md">
                                        {{ $row['clock_out'] }}
                                    </span>
                                @else
                                    <span class="text-xs text-neutral-400 italic">Belum Absen</span>
                                @endif
                            </div>

                            {{-- KOLOM 4: STATUS --}}
                            <div class="flex justify-end mt-2 md:mt-0">
                                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $row['color_class'] }}">
                                    {{ $row['status'] }}
                                </span>
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