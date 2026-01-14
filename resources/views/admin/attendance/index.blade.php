<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-800 dark:text-neutral-200 leading-tight">
            {{ __('Monitoring Absensi Harian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-neutral-800 overflow-hidden shadow-xl sm:rounded-2xl p-6 border border-neutral-100 dark:border-neutral-700">
                
                {{-- Flash Message Success --}}
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-900/30 border-l-4 border-green-400 text-green-700 dark:text-green-300 shadow-sm flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button @click="show = false" class="text-green-700 dark:text-green-300 hover:text-green-900 font-bold">&times;</button>
                    </div>
                @endif

                <div class="flex flex-col md:flex-row justify-between items-end gap-4 mb-8 p-5 bg-neutral-50 dark:bg-neutral-700/30 rounded-2xl border border-neutral-200 dark:border-neutral-700">
                    
                    {{-- FORM FILTER --}}
                    <form method="GET" action="{{ route('admin.attendance.report') }}" class="flex flex-col sm:flex-row sm:items-end gap-4 w-full md:w-auto">
                        
                        {{-- 1. Filter Pegawai --}}
                        <div class="w-full sm:w-48">
                            <label for="user_id" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">{{ __('Pegawai') }}</label>
                            <select id="user_id" name="user_id" class="block w-full h-[42px] px-3 py-2 text-sm border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                                <option value="">-- Semua --</option>
                                @foreach($allUsers as $u)
                                    <option value="{{ $u->id }}" {{ $selectedUserId == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 2. Filter Status --}}
                        <div class="w-full sm:w-40">
                            <label for="status" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">{{ __('Status') }}</label>
                            <select id="status" name="status" class="block w-full h-[42px] px-3 py-2 text-sm border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                                <option value="">-- Semua --</option>
                                <option value="Hadir" {{ $selectedStatus == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                                <option value="Terlambat" {{ $selectedStatus == 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
                                <option value="Pulang Cepat" {{ $selectedStatus == 'Pulang Cepat' ? 'selected' : '' }}>Pulang Cepat</option>
                                <option value="Mangkir" {{ $selectedStatus == 'Mangkir' ? 'selected' : '' }}>Mangkir</option>
                                <option value="Cuti" {{ $selectedStatus == 'Cuti' ? 'selected' : '' }}>Cuti/Izin</option>
                            </select>
                        </div>

                        {{-- 3. Filter Tanggal --}}
                        <div class="flex gap-2 w-full sm:w-auto">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">Dari</label>
                                <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="block w-full h-[42px] px-3 py-2 text-sm border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm" />
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">Sampai</label>
                                <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="block w-full h-[42px] px-3 py-2 text-sm border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm" />
                            </div>
                        </div>

                        {{-- Tombol Filter --}}
                        <button type="submit" class="h-[42px] px-6 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-xs uppercase tracking-widest shadow-md transition ease-in-out duration-150">
                            Filter
                        </button>
                    </form>

                    {{-- TOMBOL SYNC (BARU) --}}
                    {{-- Diletakkan di sebelah kanan form filter --}}
                    <div class="w-full md:w-auto flex justify-end">
                        <form action="{{ route('admin.attendance.sync') }}" method="POST" onsubmit="return confirm('Proses ini akan menghitung ulang data absensi untuk rentang tanggal yang dipilih. Lanjutkan?');">
                            @csrf
                            {{-- Kirim tanggal yang sedang difilter agar sync sesuai view --}}
                            <input type="hidden" name="start_date" value="{{ $startDate }}">
                            <input type="hidden" name="end_date" value="{{ $endDate }}">
                            
                            <button type="submit" class="h-[42px] inline-flex items-center px-4 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-lg font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Sync Data
                            </button>
                        </form>
                    </div>

                </div>

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

                    {{-- 4. Data Tidak Lengkap --}}
                    <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-xl border border-purple-100 dark:border-purple-800 flex flex-col items-center justify-center">
                        <span class="text-2xl font-bold text-purple-700 dark:text-purple-400">{{ $summary['belum_datang'] + $summary['belum_pulang'] }}</span>
                        <span class="text-xs font-medium text-purple-600 dark:text-purple-300 uppercase tracking-wider mt-1 text-center">Data Tdk Lengkap</span>
                    </div>

                    {{-- 5. Cuti / Izin --}}
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl border border-blue-100 dark:border-blue-800 flex flex-col items-center justify-center">
                        <span class="text-2xl font-bold text-blue-700 dark:text-blue-400">{{ $summary['total_cuti'] + $summary['total_sakit'] }}</span>
                        <span class="text-xs font-medium text-blue-600 dark:text-blue-300 uppercase tracking-wider mt-1 text-center">Cuti / Sakit</span>
                    </div>

                    {{-- 6. Mangkir --}}
                    <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-xl border border-red-100 dark:border-red-800 flex flex-col items-center justify-center">
                        <span class="text-2xl font-bold text-red-700 dark:text-red-400">{{ $summary['mangkir'] }}</span>
                        <span class="text-xs font-medium text-red-600 dark:text-red-300 uppercase tracking-wider mt-1 text-center">Mangkir</span>
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
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-neutral-800 dark:text-neutral-200">{{ \Carbon\Carbon::parse($row['date'])->format('d M Y') }}</span>
                                            <span class="text-xs text-neutral-500 font-medium uppercase tracking-wide">{{ $row['day_name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-neutral-700 dark:text-neutral-300 font-medium">
                                        {{ $row['user_name'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($row['clock_in'] !== '-')
                                            <span class="px-3 py-1 text-xs font-mono font-bold text-green-700 bg-green-100 border border-green-200 rounded-md">{{ $row['clock_in'] }}</span>
                                        @else
                                            <span class="text-neutral-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($row['clock_out'] !== '-')
                                            <span class="px-3 py-1 text-xs font-mono font-bold text-orange-700 bg-orange-100 border border-orange-200 rounded-md">{{ $row['clock_out'] }}</span>
                                        @else
                                            <span class="text-neutral-300">-</span>
                                        @endif
                                    </td>
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