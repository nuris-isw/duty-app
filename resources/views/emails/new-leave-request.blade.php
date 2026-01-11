<x-mail::message>
# Pengajuan Cuti Baru

Halo Admin/Atasan,

Terdapat pengajuan cuti baru dari pegawai **{{ $leaveRequest->user->name }}**. Mohon ditinjau.

<x-mail::panel>
Jenis Cuti: **{{ $leaveRequest->leave_type }}**
</x-mail::panel>

## Detail Pengajuan

<x-mail::table>
| Informasi | Keterangan |
| :--- | :--- |
| **Nama Pegawai** | {{ $leaveRequest->user->name }} |
| **Mulai Tanggal** | {{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') }} |
| **Sampai Tanggal** | {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') }} |
| **Total Hari** | {{ \Carbon\Carbon::parse($leaveRequest->start_date)->diffInDays(\Carbon\Carbon::parse($leaveRequest->end_date)) + 1 }} Hari |
| **Alasan** | {{ $leaveRequest->reason ?? '-' }} |
</x-mail::table>

Silakan klik tombol di bawah untuk menyetujui atau menolak pengajuan ini.

<x-mail::button :url="route('admin.leave-requests.index')" color="primary">
Tinjau Pengajuan
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>