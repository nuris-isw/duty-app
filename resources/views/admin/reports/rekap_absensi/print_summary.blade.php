<!DOCTYPE html>
<html>
<head>
    <title>Rekap Total Absensi</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; padding: 0; }
        .header p { margin: 5px 0; font-size: 12px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #444; padding: 6px; text-align: center; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-left { text-align: left; }
        
        /* Warna Status (Opsional, DomPDF support warna basic) */
        .bg-green { background-color: #d1fae5; }
        .bg-orange { background-color: #ffedd5; }
        .bg-yellow { background-color: #fef3c7; }
        .bg-blue { background-color: #e0f2fe; }
        .bg-red { background-color: #ffe4e6; color: #881337; }
        .text-muted { color: #999; }
    </style>
</head>
<body>

    <div class="header">
        <h2>REKAPITULASI TOTAL ABSENSI PEGAWAI</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="30%">Nama Pegawai</th>
                <th>Hadir</th>
                <th>Telat</th>
                <th>Plg Awal</th>
                <th>No In</th>
                <th>No Out</th>
                <th>Cuti</th>
                <th>Sakit</th>
                <th>Mangkir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $index => $user)
                @php $sum = $summaryData[$user->id]; @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-left">{{ $user->name }}</td>
                    
                    <td class="{{ $sum['hadir'] > 0 ? 'bg-green' : 'text-muted' }}">{{ $sum['hadir'] }}</td>
                    <td class="{{ $sum['terlambat'] > 0 ? 'bg-orange' : 'text-muted' }}">{{ $sum['terlambat'] > 0 ? $sum['terlambat'] : '-' }}</td>
                    <td class="{{ $sum['pulang_awal'] > 0 ? 'bg-orange' : 'text-muted' }}">{{ $sum['pulang_awal'] > 0 ? $sum['pulang_awal'] : '-' }}</td>
                    <td class="{{ $sum['no_in'] > 0 ? 'bg-yellow' : 'text-muted' }}">{{ $sum['no_in'] > 0 ? $sum['no_in'] : '-' }}</td>
                    <td class="{{ $sum['no_out'] > 0 ? 'bg-yellow' : 'text-muted' }}">{{ $sum['no_out'] > 0 ? $sum['no_out'] : '-' }}</td>
                    <td class="{{ $sum['cuti'] > 0 ? 'bg-blue' : 'text-muted' }}">{{ $sum['cuti'] > 0 ? $sum['cuti'] : '-' }}</td>
                    <td class="{{ $sum['sakit'] > 0 ? 'bg-blue' : 'text-muted' }}">{{ $sum['sakit'] > 0 ? $sum['sakit'] : '-' }}</td>
                    <td class="{{ $sum['mangkir'] > 0 ? 'bg-red' : 'text-muted' }}">{{ $sum['mangkir'] > 0 ? $sum['mangkir'] : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right; font-size: 10px;">
        Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
    </div>

</body>
</html>