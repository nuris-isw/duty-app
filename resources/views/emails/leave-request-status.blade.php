<!DOCTYPE html>
<html>
<head>
    <title>Pembaruan Status Pengajuan Cuti</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <h2>Pemberitahuan Status Pengajuan Cuti</h2>
    <p>Halo, {{ $leaveRequest->user->name }},</p>
    <p>
        Pengajuan cuti Anda untuk tanggal 
        <strong>{{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') }}</strong> 
        sampai 
        <strong>{{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') }}</strong> 
        telah direspon.
    </p>
    <p>
        Status baru pengajuan Anda adalah: 
        <strong style="text-transform: capitalize;">{{ $leaveRequest->status }}</strong>
    </p>
    <p>
        Terima kasih.
    </p>
</body>
</html>