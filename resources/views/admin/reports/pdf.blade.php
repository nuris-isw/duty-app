<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengajuan Cuti</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; }
        .table th { background-color: #f2f2f2; text-align: left; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Rekapitulasi Izin & Cuti</h2>
        @if ($startDate && $endDate)
            <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        @else
            <p>Periode: Semua Data</p>
        @endif
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Nama Pegawai</th>
                <th>Jabatan</th>
                <th>Jenis Cuti</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($leaveRequests as $request)
                <tr>
                    <td>{{ $request->user->name ?? 'N/A' }}</td>
                    <td>{{ $request->user->jabatan->nama_jabatan ?? 'N/A' }} {{ $request->user->jabatan->alias ?? 'N/A' }}</td>
                    <td>{{ $request->leave_type }}</td>
                    <td>{{ \Carbon\Carbon::parse($request->start_date)->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($request->end_date)->format('d-m-Y') }}</td>
                    <td>{{ ucfirst($request->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>