<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-800 dark:text-neutral-200 leading-tight">
            {{ __('Heatmap Absensi Pegawai') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-[98%] mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-neutral-800 overflow-hidden shadow-sm sm:rounded-xl p-6 border border-neutral-100 dark:border-neutral-700">
                
                {{-- Bagian Header & Filter --}}
                <div class="flex flex-col lg:flex-row justify-between items-end mb-6 gap-4 p-4 bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm">
    
                    {{-- KIRI: Form Filter --}}
                    <form method="GET" action="{{ route('admin.rekap-absensi.index') }}" class="flex flex-col sm:flex-row items-end gap-3 w-full lg:w-auto">
                        <div class="w-full sm:w-auto">
                            <label class="text-[10px] font-bold text-neutral-500 uppercase tracking-wide mb-1 block">Dari Tanggal</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" 
                                class="block w-full sm:w-40 h-10 text-sm rounded-lg border-neutral-300 dark:border-neutral-600 focus:ring-indigo-500 dark:bg-neutral-900 dark:text-white" required />
                        </div>
                        <div class="w-full sm:w-auto">
                            <label class="text-[10px] font-bold text-neutral-500 uppercase tracking-wide mb-1 block">Sampai Tanggal</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" 
                                class="block w-full sm:w-40 h-10 text-sm rounded-lg border-neutral-300 dark:border-neutral-600 focus:ring-indigo-500 dark:bg-neutral-900 dark:text-white" required />
                        </div>
                        <button type="submit" class="h-10 px-6 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition shadow-sm flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filter Data
                        </button>
                    </form>

                    {{-- KANAN: Group Tombol Cetak --}}
                    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto border-t lg:border-t-0 lg:border-l border-neutral-100 dark:border-neutral-700 pt-4 lg:pt-0 lg:pl-4">
                        
                        {{-- Tombol PDF Tabel 1 (Rekap Total) --}}
                        <a href="{{ route('admin.rekap-absensi.print-summary', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank"
                        class="h-10 px-4 bg-white dark:bg-neutral-700 border border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-200 text-sm font-medium rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-600 transition flex items-center justify-center gap-2 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>PDF Rekap Total</span>
                        </a>

                        {{-- Tombol PDF Tabel 2 (Matriks/Heatmap) --}}
                        <a href="{{ route('admin.rekap-absensi.print-matrix', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" 
                        class="h-10 px-4 bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium rounded-lg transition flex items-center justify-center gap-2 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span>PDF Matriks</span>
                        </a>

                    </div>
                </div>

                {{-- Tabel Rekapitulasi Total --}}
                <div class="mb-8 overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm">
                    <div class="p-4 bg-neutral-50 dark:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700">
                        <h3 class="font-bold text-lg text-neutral-800 dark:text-neutral-200">Rekapitulasi Absensi</h3>
                    </div>
                    <table class="w-full text-sm text-center border-collapse bg-white dark:bg-neutral-900">
                        <thead class="bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="p-3 border-b dark:border-neutral-700 w-10">No</th>
                                <th class="p-3 border-b dark:border-neutral-700 text-left">Nama Pegawai</th>
                                <th class="p-3 border-b dark:border-neutral-700 text-emerald-600">Hadir</th>
                                <th class="p-3 border-b dark:border-neutral-700 text-orange-600">Terlambat</th>
                                <th class="p-3 border-b dark:border-neutral-700 text-orange-600">Pulang Awal</th>
                                <th class="p-3 border-b dark:border-neutral-700 text-amber-600">No In</th>
                                <th class="p-3 border-b dark:border-neutral-700 text-amber-600">No Out</th>
                                <th class="p-3 border-b dark:border-neutral-700 text-blue-600">Cuti</th>
                                <th class="p-3 border-b dark:border-neutral-700 text-blue-600">Sakit</th>
                                <th class="p-3 border-b dark:border-neutral-700 text-rose-600">Mangkir</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                            @foreach($users as $index => $user)
                                @php $sum = $summaryData[$user->id]; @endphp
                                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition border-b border-neutral-100 dark:border-neutral-800">
                                    <td class="p-3 text-neutral-500">{{ $loop->iteration }}</td>
                                    <td class="p-3 text-left font-medium text-neutral-700 dark:text-neutral-300">{{ $user->name }}</td>
                                    
                                    {{-- 1. HADIR (Emerald/Hijau) --}}
                                    <td class="p-3 font-bold {{ $sum['hadir'] > 0 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'text-neutral-300 dark:text-neutral-600' }}">
                                        {{ $sum['hadir'] }}
                                    </td>
                                    
                                    {{-- 2. TERLAMBAT (Orange) --}}
                                    <td class="p-3 font-bold {{ $sum['terlambat'] > 0 ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400' : 'text-neutral-300 dark:text-neutral-600' }}">
                                        {{ $sum['terlambat'] > 0 ? $sum['terlambat'] : '-' }}
                                    </td>

                                    {{-- 3. PULANG AWAL (Orange) --}}
                                    <td class="p-3 font-bold {{ $sum['pulang_awal'] > 0 ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400' : 'text-neutral-300 dark:text-neutral-600' }}">
                                        {{ $sum['pulang_awal'] > 0 ? $sum['pulang_awal'] : '-' }}
                                    </td>
                                    
                                    {{-- 4. NO IN (Amber/Kuning Gelap) --}}
                                    <td class="p-3 font-bold {{ $sum['no_in'] > 0 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'text-neutral-300 dark:text-neutral-600' }}">
                                        {{ $sum['no_in'] > 0 ? $sum['no_in'] : '-' }}
                                    </td>

                                    {{-- 5. NO OUT (Amber/Kuning Gelap) --}}
                                    <td class="p-3 font-bold {{ $sum['no_out'] > 0 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'text-neutral-300 dark:text-neutral-600' }}">
                                        {{ $sum['no_out'] > 0 ? $sum['no_out'] : '-' }}
                                    </td>
                                    
                                    {{-- 6. CUTI (Sky Blue) --}}
                                    <td class="p-3 font-bold {{ $sum['cuti'] > 0 ? 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400' : 'text-neutral-300 dark:text-neutral-600' }}">
                                        {{ $sum['cuti'] > 0 ? $sum['cuti'] : '-' }}
                                    </td>

                                    {{-- 7. SAKIT (Sky Blue) --}}
                                    <td class="p-3 font-bold {{ $sum['sakit'] > 0 ? 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400' : 'text-neutral-300 dark:text-neutral-600' }}">
                                        {{ $sum['sakit'] > 0 ? $sum['sakit'] : '-' }}
                                    </td>
                                    
                                    {{-- 8. MANGKIR (Rose/Merah) --}}
                                    <td class="p-3 font-bold {{ $sum['mangkir'] > 0 ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400' : 'text-neutral-300 dark:text-neutral-600' }}">
                                        {{ $sum['mangkir'] > 0 ? $sum['mangkir'] : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Tabel Heatmap --}}
                <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm">
                    <div class="p-4 bg-neutral-50 dark:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700 flex flex-col md:flex-row justify-between items-center gap-4">
        
                        <h3 class="font-bold text-lg text-neutral-800 dark:text-neutral-200 whitespace-nowrap">
                            Matriks Absensi
                        </h3>

                        {{-- Legenda --}}
                        <div class="flex flex-wrap justify-center md:justify-end gap-x-4 gap-y-2 text-xs text-neutral-600 dark:text-neutral-400 bg-white dark:bg-neutral-900 p-2 px-3 rounded-lg border border-dashed border-neutral-300 dark:border-neutral-700">
                            <div class="flex items-center"><span class="w-3 h-3 bg-emerald-100 border border-emerald-300 rounded-sm mr-2"></span> Hadir</div>
                            <div class="flex items-center"><span class="w-3 h-3 bg-orange-100 border border-orange-300 rounded-sm mr-2"></span> Terlambat</div>
                            <div class="flex items-center"><span class="w-3 h-3 bg-amber-100 border border-amber-300 rounded-sm mr-2"></span> Data Kurang</div>
                            <div class="flex items-center"><span class="w-3 h-3 bg-sky-100 border border-sky-300 rounded-sm mr-2"></span> Cuti</div>
                            <div class="flex items-center"><span class="w-3 h-3 bg-rose-500 border border-rose-600 rounded-sm mr-2"></span> Mangkir</div>
                            <div class="flex items-center"><span class="w-3 h-3 bg-indigo-50 border border-indigo-200 rounded-sm mr-2"></span> L. Nasional</div>
                            <div class="flex items-center"><span class="w-3 h-3 bg-slate-100 border border-slate-200 rounded-sm mr-2"></span> Akhir Pekan</div>
                        </div>
                        
                    </div>

                    <table class="w-full text-xs text-center border-collapse bg-white dark:bg-neutral-900">
                        <thead class="bg-neutral-50 dark:bg-neutral-800 text-neutral-500 dark:text-neutral-400 font-semibold uppercase tracking-wider sticky top-0 z-20 shadow-sm">
                            <tr>
                                <th class="p-4 w-12 sticky left-0 bg-neutral-50 dark:bg-neutral-800 border-b border-r border-neutral-200 dark:border-neutral-700 z-30">No</th>
                                <th class="p-4 w-64 text-left sticky left-12 bg-neutral-50 dark:bg-neutral-800 border-b border-r border-neutral-200 dark:border-neutral-700 z-30">Nama Pegawai</th>
                                
                                {{-- Loop Tanggal --}}
                                @foreach($period as $date)
                                    <th class="p-0.5 min-w-[30px] border-b border-neutral-200 dark:border-neutral-700 {{ $date->isWeekend() ? 'bg-slate-50 dark:bg-neutral-800/50' : '' }}">
                                        <div class="flex flex-col items-center justify-center h-full">
                                            <span class="text-[10px] {{ $date->isWeekend() ? 'text-red-400' : '' }}">{{ $date->format('D') }}</span>
                                            <span class="text-sm font-bold text-neutral-700 dark:text-neutral-300">{{ $date->format('d') }}</span>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                            @forelse($users as $index => $user)
                                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition duration-150 ease-in-out group">
                                    {{-- Kolom No --}}
                                    <td class="p-3 sticky left-0 bg-white dark:bg-neutral-900 border-r border-neutral-100 dark:border-neutral-800 z-10 font-medium text-neutral-400 group-hover:bg-neutral-50 dark:group-hover:bg-neutral-800">
                                        {{ $loop->iteration }}
                                    </td>
                                    
                                    {{-- Kolom Nama --}}
                                    <td class="p-3 text-left sticky left-12 bg-white dark:bg-neutral-900 border-r border-neutral-100 dark:border-neutral-800 z-10 font-medium text-neutral-700 dark:text-neutral-300 whitespace-nowrap group-hover:bg-neutral-50 dark:group-hover:bg-neutral-800">
                                        {{ $user->name }}
                                    </td>
                                    
                                    {{-- Loop Data Harian --}}
                                    @foreach($period as $date)
                                        @php 
                                            $data = $recap[$user->id][$date->format('Y-m-d')];
                                            $baseClass = "h-7 w-7 mx-auto rounded-md flex items-center justify-center transition-all duration-200 cursor-default relative group/cell border";
                                            $colorClass = '';
                                            $icon = '';

                                            switch($data['color']) {
                                                // Hadir (Hijau Sangat Lembut + Border)
                                                case 'green':  
                                                    $colorClass = 'bg-emerald-100 border-emerald-200 text-emerald-600 dark:bg-emerald-900/30 dark:border-emerald-800 dark:text-emerald-400';
                                                    $icon = '✓'; 
                                                    break;
                                                    
                                                // Terlambat (Orange Lembut)
                                                case 'orange': 
                                                    $colorClass = 'bg-orange-100 border-orange-200 text-orange-600 dark:bg-orange-900/30 dark:border-orange-800 dark:text-orange-400'; 
                                                    $icon = '!';
                                                    break;
                                                    
                                                // Data Tidak Lengkap (Kuning/Amber)
                                                case 'yellow': 
                                                    $colorClass = 'bg-amber-100 border-amber-200 text-amber-600 dark:bg-amber-900/30 dark:border-amber-800 dark:text-amber-400'; 
                                                    $icon = '?';
                                                    break;
                                                    
                                                // Cuti (Biru Langit)
                                                case 'blue':   
                                                    $colorClass = 'bg-sky-100 border-sky-200 text-sky-600 dark:bg-sky-900/30 dark:border-sky-800 dark:text-sky-400'; 
                                                    $icon = 'i';
                                                    break;
                                                    
                                                // Mangkir (Merah Rose Solid - Karena ini Alert Utama)
                                                case 'red':    
                                                    $colorClass = 'bg-rose-500 border-rose-600 text-white shadow-sm dark:bg-rose-600'; 
                                                    $icon = '✕';
                                                    break;
                                                    
                                                // BARU: Libur Nasional (Sedikit Kebiruan / Indigo)
                                                case 'holiday':
                                                    $colorClass = 'bg-indigo-50 border-indigo-200 text-indigo-500 dark:bg-indigo-900/20 dark:border-indigo-800 dark:text-indigo-300';
                                                    $icon = '★'; // Ikon Bintang untuk hari spesial
                                                    break;

                                                // Libur Akhir Pekan (Tetap Abu)
                                                case 'gray':   
                                                    $colorClass = 'bg-slate-50 border-transparent text-slate-300 dark:bg-neutral-800 dark:text-neutral-600'; 
                                                    $icon = '-';
                                                    break;
                                                    
                                                default:       
                                                    $colorClass = 'bg-white border-transparent';
                                            }
                                        @endphp
                                        
                                        <td class="p-1 {{ $date->isWeekend() ? 'bg-slate-50/50 dark:bg-neutral-800/20' : '' }}">
                                            <div class="{{ $baseClass }} {{ $colorClass }}">
                                                <span class="text-[10px] font-bold">{{ $icon }}</span>
                                                
                                                {{-- Tooltip Modern --}}
                                                @if($data['color'] !== 'gray')
                                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover/cell:block w-max max-w-[200px] z-50">
                                                    <div class="bg-neutral-800 text-white text-xs rounded py-1 px-2 shadow-lg text-center">
                                                        <div class="font-bold border-b border-neutral-600 pb-1 mb-1">{{ $date->format('d M') }}</div>
                                                        {{ $data['tooltip'] }}
                                                    </div>
                                                    {{-- Arrow Tooltip --}}
                                                    <div class="w-0 h-0 border-l-[4px] border-l-transparent border-r-[4px] border-r-transparent border-t-[4px] border-t-neutral-800 mx-auto"></div>
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $period->count() + 2 }}" class="p-8 text-center text-neutral-400">
                                        Belum ada data pegawai untuk periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Footer Info --}}
                <div class="mt-4 text-right text-xs text-neutral-400">
                    Data diambil pada {{ now()->translatedFormat('d F Y H:i') }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>