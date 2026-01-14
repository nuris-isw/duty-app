<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-800 dark:text-neutral-200 leading-tight">
            {{ __('Riwayat Koreksi Absensi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Header & Tombol --}}
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">Daftar Pengajuan Saya</h3>
                <a href="{{ route('attendance-correction.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Ajukan Koreksi Baru
                </a>
            </div>

            {{-- Flash Message --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r shadow-sm flex justify-between">
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="text-green-700 font-bold">&times;</button>
                </div>
            @endif

            {{-- Tabel Data --}}
            <div class="bg-white dark:bg-neutral-800 shadow-sm rounded-xl overflow-hidden border border-neutral-200 dark:border-neutral-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                        <thead class="bg-neutral-50 dark:bg-neutral-900">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Tanggal Absen</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-500 uppercase tracking-wider">Usulan Waktu</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-neutral-500 uppercase tracking-wider">Alasan</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-neutral-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-neutral-800 divide-y divide-neutral-200 dark:divide-neutral-700">
                            @forelse ($requests as $req)
                                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition">
                                    {{-- Tanggal --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900 dark:text-neutral-100">
                                        {{ \Carbon\Carbon::parse($req->date)->translatedFormat('d M Y') }}
                                    </td>
                                    
                                    {{-- Usulan --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-neutral-600 dark:text-neutral-300">
                                        <div class="flexflex-col gap-1">
                                            @if($req->proposed_start_time)
                                                <div class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded border border-blue-100 inline-block mb-1">
                                                    Masuk: {{ \Carbon\Carbon::parse($req->proposed_start_time)->format('H:i') }}
                                                </div>
                                            @endif
                                            @if($req->proposed_end_time)
                                                <div class="text-xs bg-purple-50 text-purple-700 px-2 py-0.5 rounded border border-purple-100 inline-block">
                                                    Pulang: {{ \Carbon\Carbon::parse($req->proposed_end_time)->format('H:i') }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Alasan --}}
                                    <td class="px-6 py-4 text-sm text-neutral-600 dark:text-neutral-300 min-w-[200px]">
                                        <div class="font-medium text-neutral-800 dark:text-neutral-200 truncate max-w-xs" title="{{ $req->reason }}">
                                            {{ $req->reason }}
                                        </div>
                                        @if($req->dokumen_pendukung)
                                            <a href="{{ asset('storage/' . $req->dokumen_pendukung) }}" target="_blank" class="text-xs text-indigo-600 hover:underline flex items-center mt-1">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                Lihat Bukti
                                            </a>
                                        @endif
                                        @if($req->status == 'rejected' && $req->rejection_reason)
                                            <div class="text-xs text-red-500 mt-2 p-1 bg-red-50 rounded border border-red-100">
                                                <strong>Alasan Tolak:</strong> {{ $req->rejection_reason }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $badges = [
                                                'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                'rejected' => 'bg-rose-100 text-rose-800 border-rose-200'
                                            ];
                                            $labels = [
                                                'pending' => 'Menunggu',
                                                'approved' => 'Disetujui',
                                                'rejected' => 'Ditolak'
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full border {{ $badges[$req->status] ?? 'bg-gray-100' }}">
                                            {{ $labels[$req->status] ?? ucfirst($req->status) }}
                                        </span>
                                    </td>

                                    {{-- Tgl Pengajuan --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-neutral-500">
                                        {{ $req->created_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-neutral-500">
                                        <svg class="mx-auto h-12 w-12 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <span class="mt-2 block text-sm font-medium">Belum ada pengajuan koreksi.</span>
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