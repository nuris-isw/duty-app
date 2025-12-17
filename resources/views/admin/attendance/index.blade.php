<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-800 dark:text-neutral-200 leading-tight">
            {{ __('Monitoring Absensi Harian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-neutral-800 overflow-hidden shadow-xl sm:rounded-2xl p-6 border border-neutral-100 dark:border-neutral-700">
                
                {{-- Form Filter --}}
                <form method="GET" action="{{ route('admin.attendance.report') }}" class="mb-8 flex flex-wrap gap-4 items-end justify-between border-b border-neutral-100 dark:border-neutral-700 pb-6">
                    <div class="flex gap-4">
                        <div>
                            <x-input-label for="start_date" :value="__('Dari Tanggal')" />
                            <x-text-input id="start_date" class="block mt-1" type="date" name="start_date" :value="$startDate" required />
                        </div>
                        <div>
                            <x-input-label for="end_date" :value="__('Sampai Tanggal')" />
                            <x-text-input id="end_date" class="block mt-1" type="date" name="end_date" :value="$endDate" required />
                        </div>
                    </div>
                    <x-primary-button class="h-10 px-6">
                        {{ __('Tampilkan Data') }}
                    </x-primary-button>
                </form>

                {{-- Tabel Laporan --}}
                <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
                    <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-sm">
                        <thead class="bg-neutral-900 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left font-semibold uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-4 text-left font-semibold uppercase tracking-wider">Nama Pegawai</th>
                                <th class="px-6 py-4 text-center font-semibold uppercase tracking-wider">Jam Masuk</th>
                                <th class="px-6 py-4 text-center font-semibold uppercase tracking-wider">Jam Pulang</th>
                                <th class="px-6 py-4 text-center font-semibold uppercase tracking-wider">Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-neutral-800 divide-y divide-neutral-200 dark:divide-neutral-700">
                            @forelse($attendanceReport as $row)
                                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition duration-150">
                                    {{-- Tanggal --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-neutral-800 dark:text-neutral-200">
                                                {{ \Carbon\Carbon::parse($row['date'])->format('d M Y') }}
                                            </span>
                                            <span class="text-xs text-neutral-500 font-medium uppercase tracking-wide">
                                                {{ $row['day_name'] }}
                                            </span>
                                        </div>
                                    </td>
                                    
                                    {{-- Nama --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-neutral-700 dark:text-neutral-300 font-medium">
                                        {{ $row['user_name'] }}
                                    </td>

                                    {{-- Jam Masuk --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($row['clock_in'] !== '-')
                                            <span class="px-3 py-1 text-xs font-mono font-bold text-green-700 bg-green-100 border border-green-200 rounded-md">
                                                {{ $row['clock_in'] }}
                                            </span>
                                        @else
                                            <span class="text-neutral-300">-</span>
                                        @endif
                                    </td>

                                    {{-- Jam Pulang --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($row['clock_out'] !== '-')
                                            <span class="px-3 py-1 text-xs font-mono font-bold text-orange-700 bg-orange-100 border border-orange-200 rounded-md">
                                                {{ $row['clock_out'] }}
                                            </span>
                                        @else
                                            <span class="text-neutral-300">-</span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $row['color_class'] }}">
                                            {{ $row['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-neutral-400 italic">
                                        Tidak ada data absensi yang ditemukan pada rentang tanggal ini.
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