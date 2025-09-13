<!DOCTYPE html>
<html>
<head>
    <title>Pengajuan Cuti Baru</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <h2>Pemberitahuan Pengajuan Cuti Baru</h2>
    <p>Halo,</p>
    <p>
        Anda telah menerima pengajuan cuti baru dari pegawai Anda, 
        <strong>{{ $leaveRequest->user->name }}</strong>.
    </p>
    <p>
        <strong>Detail Pengajuan:</strong>
    </p>
    <ul>
        <li><strong>Jenis Cuti:</strong> {{ $leaveRequest->leave_type }}</li>
        <li><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') }}</li>
        <li><strong>Alasan:</strong> {{ $leaveRequest->reason }}</li>
    </ul>
    <p>
        Silakan login ke aplikasi untuk meninjau dan merespon pengajuan ini.
    </p>
    <p>
        Terima kasih.
    </p>
</body>
</html>