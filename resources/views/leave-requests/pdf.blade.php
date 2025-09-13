<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Izin Cuti</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h3, .header p { margin: 0; }
        .content { margin-top: 30px; }
        .signatures { margin-top: 60px; width: 100%; }
        .signatures td { width: 50%; text-align: center; }
        .signatures img { max-height: 60px; }
    </style>
</head>
<body>
    <div class="header">
        <h3>SURAT KETERANGAN IZIN / CUTI</h3>
        <p>Nomor: {{ $leaveRequest->id }}/IZIN/IX/2025</p>
    </div>

    <div class="content">
        <p>Dengan hormat,</p>
        <p>Yang bertanda tangan di bawah ini menerangkan bahwa:</p>
        
        <table>
            <tr>
                <td style="width: 120px;">Nama</td>
                <td>: {{ $leaveRequest->user->name }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>: {{ $leaveRequest->user->jabatan->nama_jabatan ?? '-' }} {{ $leaveRequest->user->jabatan->bidang_kerja ?? '' }}</td>
            </tr>
             <tr>
                <td>Unit Kerja</td>
                <td>: {{ $leaveRequest->user->unitKerja->nama_unit ?? '-' }}</td>
            </tr>
        </table>

        <p>Diberikan izin/cuti "{{ $leaveRequest->leave_type }}" selama periode:</p>
        <p><strong>Tanggal Mulai:</strong> {{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d F Y') }}</p>
        <p><strong>Tanggal Selesai:</strong> {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d F Y') }}</p>
        <p><strong>Alasan:</strong> {{ $leaveRequest->reason }}</p>

        <p>Demikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <table class="signatures">
        <tr>
            <td>
                <p>Menyetujui,</p>
                <p>{{ $leaveRequest->approver->jabatan->nama_jabatan ?? '-' }} {{ $leaveRequest->user->jabatan->alias ?? '' }}</p>
                <br><br>
                {{-- Cek relasi 'approver' dan apakah signature-nya ada --}}
                @if ($leaveRequest->approver && $leaveRequest->approver->signature)
                    <img src="{{ storage_path('app/public/' . $leaveRequest->approver->signature) }}" alt="TTD Atasan">
                @else
                    <p>(Belum ada TTD)</p>
                @endif
                <p><strong>{{ $leaveRequest->approver->name ?? 'Atasan' }}</strong></p>
            </td>
            <td>
                <p>-</p>
                <p>Pemohon,</p>
                <br><br>
                @if ($leaveRequest->user->signature)
                    <img src="{{ storage_path('app/public/' . $leaveRequest->user->signature) }}" alt="TTD Pemohon">
                @else
                    <p>(Belum ada TTD)</p>
                @endif
                <p><strong>{{ $leaveRequest->user->name }}</strong></p>
            </td>
        </tr>
    </table>
</body>
</html>