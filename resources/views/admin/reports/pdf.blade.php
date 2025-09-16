<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Pengajuan Cuti</title>
    <style>
        /* Menggunakan font yang umum tersedia di sistem untuk PDF */
        @page {
            margin: 0; /* Menghilangkan margin default halaman */
        }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 10px; 
            margin: 0;
            padding: 0;
            color: #333;
        }

        /* Posisi Header Gambar (Kop Surat) */
        #header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            height: 100px; /* Sesuaikan tinggi header jika perlu */
        }
        #header img {
            width: 100%;
            height: 100%;
        }

        /* Posisi Footer Gambar */
        #footer {
            position: fixed; 
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            height: 220px; /* Sesuaikan tinggi footer jika perlu */
        }
        #footer img {
            width: 100%;
            height: 100%;
        }

        /* Konten Utama */
        .content {
            margin-top: 120px; /* Memberi ruang di bawah header, sesuaikan jika header lebih tinggi */
            margin-bottom: 100px; /* Memberi ruang di atas footer */
            padding: 0 40px; /* Memberi padding kiri-kanan pada konten */
        }
        
        .report-header { 
            text-align: center; 
            margin-bottom: 25px; 
        }
        .report-header h2 {
            margin: 0;
            font-size: 16px;
        }
        .report-header p {
            margin: 5px 0 0;
            font-size: 12px;
        }

        /* Gaya Tabel yang Lebih Modern */
        .table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .table th, .table td { 
            border: 1px solid #e2e8f0; 
            padding: 8px 10px; 
            text-align: left;
        }
        .table th { 
            background-color: #E4252C; /* Menggunakan warna brand Anda */
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }
        .table tbody tr:nth-child(even) {
            background-color: #f8fafc; /* Warna zebra-striping */
        }
        .table td.centered {
            text-align: center;
        }
    </style>
</head>
<body>
    {{-- Menggunakan path absolut ke gambar di folder public --}}
    <div id="header">
        @php
            $path = public_path('images/letter-header.png');
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        @endphp
        <img src="{{ $base64 }}" alt="Header Laporan">
    </div>

    <div id="footer">
        @php
            $path = public_path('images/letter-footer.png');
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        @endphp
        <img src="{{ $base64 }}" alt="Footer Laporan">
    </div>

    <div class="content">
        <div class="report-header">
            <h2>Laporan Rekapitulasi Izin & Cuti</h2>
            @if ($startDate && $endDate)
                <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
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
                    <th class="centered">Tanggal Mulai</th>
                    <th class="centered">Tanggal Selesai</th>
                    <th class="centered">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($leaveRequests as $request)
                    <tr>
                        <td>{{ $request->user->name ?? 'N/A' }}</td>
                        <td>{{ $request->user->jabatan->nama_jabatan ?? 'N/A' }} {{ $request->user->jabatan->alias ?? '' }}</td>
                        <td>{{ $request->leave_type }}</td>
                        <td class="centered">{{ \Carbon\Carbon::parse($request->start_date)->format('d-m-Y') }}</td>
                        <td class="centered">{{ \Carbon\Carbon::parse($request->end_date)->format('d-m-Y') }}</td>
                        <td class="centered">{{ ucfirst($request->status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="centered" style="padding: 20px;">Tidak ada data untuk periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>