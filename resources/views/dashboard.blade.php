<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-grey overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    <h3 class="font-semibold text-lg mb-4">Riwayat Pengajuan Izin/Cuti Anda</h3>

                    <a href="{{ route('leave-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        Ajukan Cuti/Izin
                    </a>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis Cuti</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Mulai</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Selesai</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($leaveRequests as $request)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $request->leave_type }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusClass = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'approved' => 'bg-green-100 text-green-800',
                                                    'rejected' => 'bg-red-100 text-red-800',
                                                ][$request->status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
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
            </div>
        </div>
    </div>
</x-app-layout>
