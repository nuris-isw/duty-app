<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-brand-dark leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Flash Message --}}
            @if (session('success'))
                <div class="p-4 rounded-xl bg-green-100 border-l-4 border-green-500 text-green-800 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- BAGIAN 1: PENGAJUAN PENDING DARI BAWAHAN (UNTUK ATASAN) -->
            @if (Auth::user()->role === 'atasan' && $subordinatePendingRequests->isNotEmpty())
                <div class="bg-brand-light shadow-md rounded-2xl p-6">
                    <h3 class="font-semibold text-lg text-brand-dark mb-4">
                        Pengajuan Cuti Perlu Persetujuan
                    </h3>
                    <div class="overflow-x-auto rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300 text-sm">
                            <thead class="bg-brand-dark text-black">
                                <tr>
                                    <th class="px-6 py-3 text-left font-medium">Nama Pemohon</th>
                                    <th class="px-6 py-3 text-left font-medium">Jenis Cuti</th>
                                    <th class="px-6 py-3 text-left font-medium">Alasan</th>
                                    <th class="px-6 py-3 text-left font-medium">Tanggal</th>
                                    <th class="px-6 py-3 text-left font-medium">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($subordinatePendingRequests as $request)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">{{ $request->user->name }}</td>
                                        <td class="px-6 py-4">{{ $request->leave_type }}</td>
                                        <td class="px-6 py-4">{{ $request->reason }}</td>
                                        <td class="px-6 py-4">
                                            {{ \Carbon\Carbon::parse($request->start_date)->format('d M') }} -
                                            {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 flex space-x-3">
                                            <form action="{{ route('leave-requests.approve', $request) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <x-primary-button>
                                                    {{ __('Setujui') }}
                                                </x-primary-button>
                                            </form>
                                            <form action="{{ route('leave-requests.reject', $request) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <x-danger-button>
                                                    {{ __('Tolak') }}
                                                </x-danger-button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- BAGIAN 2: RIWAYAT PRIBADI -->
            <div class="bg-brand-light shadow-md rounded-2xl p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-lg text-brand-dark">Riwayat Pengajuan Izin/Cuti Anda</h3>
                    <a href="{{ route('leave-requests.create') }}">
                        <x-secondary-button type="button">
                            + Ajukan Cuti/Izin
                        </x-secondary-button>
                    </a>
                </div>
                <div class="overflow-x-auto rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300 text-sm">
                        <thead class="bg-brand-dark text-black">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium">Jenis Cuti</th>
                                <th class="px-6 py-3 text-left font-medium">Tanggal</th>
                                <th class="px-6 py-3 text-left font-medium">Status</th>
                                <th class="px-6 py-3 text-left font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($myLeaveRequests as $request)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">{{ $request->leave_type }}</td>
                                    <td class="px-6 py-4">
                                        {{ \Carbon\Carbon::parse($request->start_date)->format('d M') }} -
                                        {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusClass = [
                                                'pending' => 'bg-yellow-200 text-yellow-900',
                                                'approved' => 'bg-green-200 text-green-900',
                                                'rejected' => 'bg-red-200 text-red-900',
                                            ][$request->status] ?? 'bg-gray-200 text-gray-800';
                                        @endphp
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($request->status === 'approved')
                                            <a href="{{ route('leave-requests.print', $request) }}" 
                                               class="px-3 py-1 rounded-lg bg-gray-700 text-black text-xs hover:bg-gray-800 transition"
                                               target="_blank">
                                                Cetak
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Anda belum memiliki riwayat pengajuan cuti.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- BAGIAN 3: RIWAYAT BAWAHAN -->
            @if (Auth::user()->role === 'atasan' && $subordinateHistoryRequests->isNotEmpty())
                <div class="bg-brand-light shadow-md rounded-2xl p-6">
                    <h3 class="font-semibold text-lg text-brand-dark mb-4">Riwayat Pengajuan Pegawai</h3>
                    <div class="overflow-x-auto rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300 text-sm">
                            <thead class="bg-brand-dark text-black">
                                <tr>
                                    <th class="px-6 py-3 text-left font-medium">Nama Pemohon</th>
                                    <th class="px-6 py-3 text-left font-medium">Jenis Cuti</th>
                                    <th class="px-6 py-3 text-left font-medium">Alasan</th>
                                    <th class="px-6 py-3 text-left font-medium">Tanggal</th>
                                    <th class="px-6 py-3 text-left font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($subordinateHistoryRequests as $request)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">{{ $request->user->name }}</td>
                                        <td class="px-6 py-4">{{ $request->leave_type }}</td>
                                        <td class="px-6 py-4">{{ $request->reason }}</td>
                                        <td class="px-6 py-4">
                                            {{ \Carbon\Carbon::parse($request->start_date)->format('d M') }} -
                                            {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $statusClass = [
                                                    'approved' => 'bg-green-200 text-green-900',
                                                    'rejected' => 'bg-red-200 text-red-900',
                                                ][$request->status] ?? 'bg-gray-200 text-gray-800';
                                            @endphp
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                                {{ ucfirst($request->status) }}
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
