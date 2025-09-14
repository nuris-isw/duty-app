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
            {{-- BAGIAN KIRI: PEMOHON --}}
            <td style="vertical-align: bottom; text-align: center;">
                <p>Pemohon,</p>
                {{-- Jabatan dipindah ke atas TTD --}}
                <p>{{ $leaveRequest->user->jabatan->nama_jabatan ?? '' }} {{ $leaveRequest->user->jabatan->alias ?? '' }}</p>
                <br><br>
                
                @if ($leaveRequest->user->signature)
                    <img src="{{ storage_path('app/public/' . $leaveRequest->user->signature) }}" alt="TTD Pemohon" style="max-height: 60px; display: block; margin-left: auto; margin-right: auto;">
                @else
                    <p style="height: 60px;">(Belum ada TTD)</p>
                @endif
                
                {{-- Nama dipindah ke bawah TTD --}}
                <p><strong>{{ $leaveRequest->user->name }}</strong></p>
            </td>

            {{-- BAGIAN KANAN: MENYETUJUI --}}
            <td style="vertical-align: bottom; text-align: center;">
                <p>Menyetujui,</p>
                {{-- Jabatan dipindah ke atas TTD --}}
                <p>{{ $leaveRequest->approver->jabatan->nama_jabatan ?? '' }} {{ $leaveRequest->approver->jabatan->alias ?? '' }}</p>
                <br><br>

                @if ($leaveRequest->approver && $leaveRequest->approver->signature)
                    <img src="{{ storage_path('app/public/' . $leaveRequest->approver->signature) }}" alt="TTD Atasan" style="max-height: 60px; display: block; margin-left: auto; margin-right: auto;">
                @else
                    <p style="height: 60px;">(Belum ada TTD)</p>
                @endif

                {{-- Nama dipindah ke bawah TTD --}}
                <p><strong>{{ $leaveRequest->approver->name ?? 'Atasan' }}</strong></p>
            </td>
        </tr>
    </table>
</body>
</html>