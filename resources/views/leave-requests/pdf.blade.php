<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Surat Izin Cuti - {{ $leaveRequest->user->name }}</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; line-height: 1.6; margin: 0; padding: 0; color: #000000; }
        #header { position: fixed; top: 0; left: 0; right: 0; width: 100%; }
        #footer { position: fixed; bottom: 0; left: 0; right: 0; width: 100%; }
        #header img, #footer img { width: 100%; }
        .content { margin: 120px 70px 100px 70px; }
        .letter-header { text-align: center; margin-bottom: 20px; line-height: 1.2; }
        .letter-header h3 { margin: 0; font-size: 14px; text-decoration: underline; }
        .letter-header p { margin: 2px 0 0; font-size: 12px; }
        .details-table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        .details-table td { padding: 4px 0; vertical-align: top; }
        .details-table td.label { width: 150px; }
        .signatures { margin-top: 50px; width: 100%; }
        .signatures td { width: 50%; text-align: center; }
        .signature-space { height: 60px; position: relative; }
        .signature-space img { max-height: 60px; display: block; margin: 0 auto; }
        .signature-name { font-weight: bold; text-decoration: underline; margin-top: 5px; }
    </style>
</head>
<body>
    <div id="header">
        <img src="{{ public_path('images/letter-header.png') }}" alt="Kop Surat">
    </div>

    <div id="footer">
        <img src="{{ public_path('images/letter-footer.png') }}" alt="Footer Surat">
    </div>

    <div class="content">
        <div class="letter-header">
            <h3>SURAT KETERANGAN IZIN / CUTI</h3>
            <p>Nomor: {{ $leaveRequest->id }}/IZIN/IX/2025</p>
        </div>

        <p>Dengan hormat,</p>
        <p>Yang bertanda tangan di bawah ini menerangkan bahwa:</p>
        
        <table class="details-table">
            <tr>
                <td class="label">Nama</td>
                <td>: {{ $leaveRequest->user->name }}</td>
            </tr>
            <tr>
                <td class="label">Jabatan</td>
                <td>: {{ $leaveRequest->user->jabatan->nama_jabatan ?? '-' }} {{ $leaveRequest->user->jabatan->bidang_kerja ?? '' }}</td>
            </tr>
             <tr>
                <td class="label">Unit Kerja</td>
                <td>: {{ $leaveRequest->user->unitKerja->nama_unit ?? '-' }}</td>
            </tr>
        </table>

        <p>Telah diberikan izin / cuti <strong>{{ $leaveRequest->leave_type }}</strong> terhitung mulai tanggal <strong>{{ \Carbon\Carbon::parse($leaveRequest->start_date)->isoFormat('D MMMM Y') }}</strong> sampai dengan tanggal <strong>{{ \Carbon\Carbon::parse($leaveRequest->end_date)->isoFormat('D MMMM Y') }}</strong>.</p>
        
        @if ($leaveRequest->reason)
            <p>Adapun alasan pengajuan adalah sebagai berikut: <br><em>{{ $leaveRequest->reason }}</em></p>
        @endif

        <p>Demikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya. Atas perhatiannya, diucapkan terima kasih.</p>

        <table class="signatures">
            <tr>
                {{-- BAGIAN KIRI: MENYETUJUI --}}
                <td>
                    <p>Menyetujui,</p>
                    <div class="signature-space">
                        @if ($leaveRequest->approver && $leaveRequest->approver->signature)
                            <img src="{{ public_path('storage/' . $leaveRequest->approver->signature) }}" alt="TTD Atasan">
                        @endif
                    </div>
                    <p class="signature-name">{{ $leaveRequest->approver->name ?? 'Atasan' }}</p>
                </td>
                
                {{-- BAGIAN KANAN: PEMOHON --}}
                <td>
                    <p>Banyuwangi, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                    <p>Pemohon,</p>
                    <div class="signature-space">
                        @if ($leaveRequest->user->signature)
                            <img src="{{ public_path('storage/' . $leaveRequest->user->signature) }}" alt="TTD Pemohon">
                        @endif
                    </div>
                    <p class="signature-name">{{ $leaveRequest->user->name }}</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>