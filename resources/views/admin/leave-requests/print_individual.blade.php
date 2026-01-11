<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Surat Izin Cuti - {{ $leaveRequest->user->name }}</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; line-height: 1.6; margin: 0; padding: 0; color: #000000; }
        
        /* Header & Footer Images */
        #header { position: fixed; top: 0; left: 0; right: 0; width: 100%; height: 100px; }
        #header img { width: 100%; height: 100%; }
        
        #footer { position: fixed; bottom: 0; left: 0; right: 0; width: 100%; height: 220px; }
        #footer img { width: 100%; height: 100%; }

        /* Content Area */
        .content { margin: 120px 70px 100px 70px; }
        
        /* Judul Surat */
        .letter-header { text-align: center; margin-bottom: 20px; line-height: 1.2; }
        .letter-header h3 { margin: 0; font-size: 14px; text-decoration: underline; text-transform: uppercase; }
        .letter-header p { margin: 2px 0 0; font-size: 12px; }
        
        /* Tabel Detail */
        .details-table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        .details-table td { padding: 4px 0; vertical-align: top; }
        .details-table td.label { width: 150px; }
        
        /* Tanda Tangan */
        .signatures { margin-top: 50px; width: 100%; }
        .signatures td { width: 50%; text-align: center; vertical-align: bottom; }
        .signature-space { height: 60px; margin: 10px auto; position: relative; }
        .signature-space img { max-height: 60px; max-width: 150px; display: block; margin: 0 auto; }
        .signature-name { font-weight: bold; text-decoration: underline; margin-top: 5px; }
    </style>
</head>
<body>
    {{-- Header Gambar (Base64 agar PDF stabil) --}}
    <div id="header">
        @php
            $path = public_path('images/letter-header.png');
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64Header = 'data:image/' . $type . ';base64,' . base64_encode($data);
                echo '<img src="'.$base64Header.'" alt="Header">';
            }
        @endphp
    </div>

    {{-- Footer Gambar --}}
    <div id="footer">
        @php
            $pathFooter = public_path('images/letter-footer.png');
            if (file_exists($pathFooter)) {
                $type = pathinfo($pathFooter, PATHINFO_EXTENSION);
                $data = file_get_contents($pathFooter);
                $base64Footer = 'data:image/' . $type . ';base64,' . base64_encode($data);
                echo '<img src="'.$base64Footer.'" alt="Footer">';
            }
        @endphp
    </div>

    <div class="content">
        <div class="letter-header">
            <h3>SURAT KETERANGAN IZIN / CUTI</h3>
            {{-- Format Nomor Surat: ID/KODE/BULAN_ROMAWI/TAHUN --}}
            @php
                $bulanRomawi = array("", "I","II","III","IV","V","VI","VII","VIII","IX","X","XI","XII");
                $bulan = date('n', strtotime($leaveRequest->created_at));
                $tahun = date('Y', strtotime($leaveRequest->created_at));
            @endphp
            <p>Nomor: {{ str_pad($leaveRequest->id, 3, '0', STR_PAD_LEFT) }}/IZIN/{{ $bulanRomawi[$bulan] }}/{{ $tahun }}</p>
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
                <td>: {{ $leaveRequest->user->jabatan->nama_jabatan ?? '-' }}</td>
            </tr>
             <tr>
                <td class="label">Unit Kerja</td>
                <td>: {{ $leaveRequest->user->unitKerja->nama_unit ?? '-' }}</td>
            </tr>
        </table>

        <p>Telah diberikan izin untuk <strong>{{ $leaveRequest->leave_type }}</strong> terhitung mulai tanggal <strong>{{ \Carbon\Carbon::parse($leaveRequest->start_date)->isoFormat('D MMMM Y') }}</strong> sampai dengan tanggal <strong>{{ \Carbon\Carbon::parse($leaveRequest->end_date)->isoFormat('D MMMM Y') }}</strong>.</p>
        
        @if ($leaveRequest->reason)
            <p>Adapun alasan pengajuan adalah sebagai berikut: <br><em>{{ $leaveRequest->reason }}</em></p>
        @endif

        <p>Demikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya. Atas perhatiannya, diucapkan terima kasih.</p>

        <table class="signatures">
            <tr>
                {{-- KIRI: ATASAN --}}
                <td>
                    <p>Menyetujui,<br>Atasan Langsung</p>
                    <div class="signature-space">
                        {{-- Logika Tanda Tangan Atasan --}}
                        @if ($leaveRequest->user->superior && $leaveRequest->user->superior->signature)
                            @php
                                $ttdPath = storage_path('app/public/' . $leaveRequest->user->superior->signature);
                            @endphp
                            @if(file_exists($ttdPath))
                                <img src="{{ $ttdPath }}" alt="TTD Atasan">
                            @endif
                        @endif
                    </div>
                    <p class="signature-name">{{ $leaveRequest->user->superior->name ?? '(Nama Atasan)' }}</p>
                </td>
                
                {{-- KANAN: PEMOHON --}}
                <td>
                    {{-- Lokasi Unit Kerja sebagai Kota surat --}}
                    <p>{{ 'Banyuwangi' }}, {{ \Carbon\Carbon::parse($leaveRequest->created_at)->isoFormat('D MMMM Y') }}</p>
                    <p>Pemohon,</p>
                    <div class="signature-space">
                        {{-- Logika Tanda Tangan Pemohon --}}
                        @if ($leaveRequest->user->signature)
                            @php
                                $ttdUserPath = storage_path('app/public/' . $leaveRequest->user->signature);
                            @endphp
                            @if(file_exists($ttdUserPath))
                                <img src="{{ $ttdUserPath }}" alt="TTD Pemohon">
                            @endif
                        @endif
                    </div>
                    <p class="signature-name">{{ $leaveRequest->user->name }}</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>