<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-800 dark:text-neutral-200 leading-tight">
            {{ __('Monitoring Absensi Harian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-neutral-800 overflow-hidden shadow-xl sm:rounded-2xl p-6 border border-neutral-100 dark:border-neutral-700">
                
                <form method="GET" action="{{ route('admin.attendance.report') }}" 
                    class="mb-8 p-5 bg-neutral-50 dark:bg-neutral-700/30 rounded-2xl border border-neutral-200 dark:border-neutral-700 flex flex-col sm:flex-row sm:items-end gap-4">

                    {{-- 1. Filter Pegawai --}}
                    <div class="w-full sm:w-64">
                        <label for="user_id" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                            {{ __('Nama Pegawai') }}
                        </label>
                        <div class="relative">
                            <select id="user_id" name="user_id" 
                                class="appearance-none block w-full h-[42px] pl-3 pr-10 py-2 text-sm border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent shadow-sm transition duration-150 ease-in-out cursor-pointer">
                                <option value="">-- Tampilkan Semua --</option>
                                @foreach($allUsers as $u)
                                    <option value="{{ $u->id }}" {{ $selectedUserId == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }}
                                    </option>
                                @endforeach
                            </select>
                            {{-- Custom Chevron Icon --}}
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-neutral-500 dark:text-neutral-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div> 
                    </div>

                    {{-- 2. Filter Status --}}
                    <div class="w-full sm:w-48">
                        <label for="status" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                            {{ __('Status') }}
                        </label>
                        <div class="relative">
                            <select id="status" name="status" 
                                class="appearance-none block w-full h-[42px] pl-3 pr-10 py-2 text-sm border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent shadow-sm transition duration-150 ease-in-out cursor-pointer">
                                <option value="">-- Semua Status --</option>
                                <option value="Hadir" {{ $selectedStatus == 'Hadir' ? 'selected' : '' }}>Hadir (Tepat Waktu)</option>
                                <option value="Terlambat" {{ $selectedStatus == 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
                                <option value="Pulang Cepat" {{ $selectedStatus == 'Pulang Cepat' ? 'selected' : '' }}>Pulang Cepat</option>
                                <option value="Belum Absen" {{ $selectedStatus == 'Belum Absen' ? 'selected' : '' }}>Data Tidak Lengkap</option>
                                <option value="Mangkir" {{ $selectedStatus == 'Mangkir' ? 'selected' : '' }}>Mangkir</option>
                                <option value="Sakit" {{ $selectedStatus == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="Cuti" {{ $selectedStatus == 'Cuti' ? 'selected' : '' }}>Cuti / Izin</option>
                                <option value="Libur" {{ $selectedStatus == 'Libur' ? 'selected' : '' }}>Libur / Lembur</option>
                            </select>
                            {{-- Custom Chevron Icon --}}
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-neutral-500 dark:text-neutral-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Filter Tanggal Mulai --}}
                    <div class="w-full sm:w-auto">
                        <label for="start_date" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                            {{ __('Dari') }}
                        </label>
                        <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" required
                            class="block w-full h-[42px] px-3 py-2 text-sm border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent shadow-sm transition duration-150 ease-in-out" />
                    </div>

                    {{-- 4. Filter Tanggal Selesai --}}
                    <div class="w-full sm:w-auto">
                        <label for="end_date" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                            {{ __('Sampai') }}
                        </label>
                        <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" required
                            class="block w-full h-[42px] px-3 py-2 text-sm border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent shadow-sm transition duration-150 ease-in-out" />
                    </div>

                    {{-- 5. Tombol Submit --}}
                    <div class="w-full sm:w-auto">
                        <button type="submit" 
                            class="h-[42px] w-full sm:w-auto inline-flex items-center justify-center px-6 py-2 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-400 focus:bg-indigo-700 dark:focus:bg-indigo-400 active:bg-indigo-900 dark:active:bg-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 transition ease-in-out duration-150 shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            {{ __('Filter Data') }}
                        </button>
                    </div>

                    {{-- 6. Tombol Rekap --}}
                    <div class="w-full sm:w-auto">
                        <a href="{{ route('admin.rekap-absensi.index') }}" 
                        class="h-[42px] w-full sm:w-auto inline-flex items-center justify-center px-6 py-2 bg-emerald-600 dark:bg-emerald-500 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 dark:hover:bg-emerald-400 focus:bg-emerald-700 dark:focus:bg-emerald-400 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition ease-in-out duration-150 shadow-md">
                            {{-- Ikon Grid/Matrix --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            {{ __('Rekap Matriks') }}
                        </a>
                    </div>

                </form>

                {{-- REKAPITULASI STATISTIK --}}
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                    {{-- 1. Kehadiran --}}
                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-xl border border-green-100 dark:border-green-800 flex flex-col items-center justify-center">
                        <span class="text-2xl font-bold text-green-700 dark:text-green-400">{{ $summary['hadir'] }}</span>
                        <span class="text-xs font-medium text-green-600 dark:text-green-300 uppercase tracking-wider mt-1 text-center">Kehadiran</span>
                    </div>

                    {{-- 2. Terlambat --}}
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-xl border border-yellow-100 dark:border-yellow-800 flex flex-col items-center justify-center">
                        <span class="text-2xl font-bold text-yellow-700 dark:text-yellow-400">{{ $summary['terlambat'] }}</span>
                        <span class="text-xs font-medium text-yellow-600 dark:text-yellow-300 uppercase tracking-wider mt-1 text-center">Terlambat</span>
                    </div>

                    {{-- 3. Pulang Cepat --}}
                    <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-xl border border-orange-100 dark:border-orange-800 flex flex-col items-center justify-center">
                        <span class="text-2xl font-bold text-orange-700 dark:text-orange-400">{{ $summary['pulang_cepat'] }}</span>
                        <span class="text-xs font-medium text-orange-600 dark:text-orange-300 uppercase tracking-wider mt-1 text-center">Pulang Awal</span>
                    </div>

                    {{-- 4. Belum Absen Datang/Pulang (Digabung agar hemat tempat, atau dipisah) --}}
                    <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-xl border border-purple-100 dark:border-purple-800 flex flex-col items-center justify-center">
                        <span class="text-2xl font-bold text-purple-700 dark:text-purple-400">{{ $summary['belum_datang'] + $summary['belum_pulang'] }}</span>
                        <span class="text-xs font-medium text-purple-600 dark:text-purple-300 uppercase tracking-wider mt-1 text-center">Data Tidak Lengkap</span>
                        {{-- Tooltip info detail --}}
                        <span class="text-[10px] text-purple-400 mt-1">
                            (Dtg: {{ $summary['belum_datang'] }} | Plg: {{ $summary['belum_pulang'] }})
                        </span>
                    </div>

                    {{-- 5. Cuti / Izin --}}
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl border border-blue-100 dark:border-blue-800 flex flex-col items-center justify-center">
                        <span class="text-2xl font-bold text-blue-700 dark:text-blue-400">{{ $summary['total_cuti'] + $summary['total_sakit'] }}</span>
                        <span class="text-xs font-medium text-blue-600 dark:text-blue-300 uppercase tracking-wider mt-1 text-center">Cuti / Sakit</span>
                        {{-- Detail Breakdown --}}
                        <span class="text-[10px] text-blue-400 mt-1">
                            (Cuti: {{ $summary['total_cuti'] }} | Sakit: {{ $summary['total_sakit'] }})
                        </span>
                    </div>

                    {{-- 6. Mangkir --}}
                    <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-xl border border-red-100 dark:border-red-800 flex flex-col items-center justify-center">
                        <span class="text-2xl font-bold text-red-700 dark:text-red-400">{{ $summary['mangkir'] }}</span>
                        <span class="text-xs font-medium text-red-600 dark:text-red-300 uppercase tracking-wider mt-1 text-center">Mangkir (Alpha)</span>
                    </div>
                </div>

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