<x-mail::message>
# Halo, {{ $leaveRequest->user->name }}

Berikut adalah pembaruan status terkait pengajuan cuti Anda.

{{-- Logika Warna: Hijau (success) jika Approved, Merah (error) jika Rejected --}}
@php
    $color = $leaveRequest->status === 'approved' ? 'success' : 'error';
    $statusText = $leaveRequest->status === 'approved' ? 'DISETUJUI' : 'DITOLAK';
    $statusDesc = $leaveRequest->status === 'approved' 
        ? 'Pengajuan cuti Anda telah disetujui.' 
        : 'Mohon maaf, pengajuan cuti Anda belum dapat disetujui saat ini.';
@endphp

<x-mail::panel>
Status: <strong style="color: {{ $leaveRequest->status === 'approved' ? '#22c55e' : '#ef4444' }}">{{ $statusText }}</strong>
<br>
{{ $statusDesc }}
</x-mail::panel>

## Ringkasan Pengajuan

<x-mail::table>
| Informasi | Keterangan |
| :--- | :--- |
| **Jenis Cuti** | {{ $leaveRequest->leave_type }} |
| **Tanggal** | {{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') }} |
| **Durasi** | {{ \Carbon\Carbon::parse($leaveRequest->start_date)->diffInDays(\Carbon\Carbon::parse($leaveRequest->end_date)) + 1 }} Hari |
</x-mail::table>

Anda dapat melihat riwayat lengkapnya di aplikasi.

<x-mail::button :url="route('my-attendance.index')" :color="$color">
Lihat Aplikasi
</x-mail::button>

Terima kasih,<br>
Divisi Operasional & Administrasi Umum
</x-mail::message>