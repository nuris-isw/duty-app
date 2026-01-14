1. Identitas Proyek
Nama Proyek: Sistem Informasi Manajemen Kepegawaian (ESS - Employee Self Service) / Sistem Pengajuan Cuti & Izin.

Tujuan Utama: Digitalisasi proses pengajuan cuti, persetujuan berjenjang (atasan-bawahan), pengelolaan kuota cuti tahunan, dan pelaporan/cetak surat izin otomatis.

Teknologi Utama:

Backend: Laravel (PHP Framework).

Frontend: Blade Templates, Tailwind CSS, Alpine.js.

Database: MySQL.

PDF Engine: barryvdh/laravel-dompdf.

Lingkungan:

Server: Shared Hosting (Hostinger/cPanel environment).

PHP Versi: Project membutuhkan PHP 8.2, namun default server PHP 8.1 (Diperlukan pemanggilan path binary khusus).

Queue Driver: Database (dijalankan via Cron Job).

2. Ruang Lingkup & Fitur
Fitur yang SUDAH Dibuat:

Dashboard: Menampilkan statistik cuti, sisa kuota, tabel approval untuk atasan, dan riwayat pengajuan user.

Pengajuan Cuti: Form dengan validasi dinamis (dokumen pendukung untuk "Sakit", alasan otomatis), perhitungan durasi kerja (skip weekend & hari libur nasional).

Approval System: Logika atasan menyetujui/menolak pengajuan bawahan.

Manajemen Kuota: Pengurangan kuota otomatis saat approved. Cuti khusus (Sakit/Dinas) kuota 0 (tidak memotong jatah tahunan).

Cetak PDF:

Laporan Rekapitulasi (Tabel).

Surat Izin Perorangan (Format Resmi dengan Tanda Tangan Digital/Image).

Notifikasi Email: Mengirim email ke Atasan saat ada pengajuan baru, dan ke User saat status berubah (menggunakan Queue).

RBAC (Role-Based Access Control): Superadmin, SysAdmin, Unit Admin, User Biasa.

Fitur yang SEDANG Dikerjakan/Diselesaikan Terakhir:

Perbaikan validasi form pengajuan (Bug reason required saat hidden).

Konfigurasi SMTP Gmail untuk pengiriman email via Queue di Shared Hosting.

Pencegahan Race Condition pada pemotongan kuota.

Fitur yang BELUM Dibuat / Rencana:

Modul Absensi (disebutkan dalam struktur route tapi belum dibahas detail).

Sinkronisasi data libur nasional (saat ini diasumsikan ada tabel holidays).

3. Struktur Teknis
Struktur Folder/File Penting:

app/Http/Controllers/LeaveRequestController.php: Controller Umum (User) untuk create, store, approve, reject (logic atasan).

app/Http/Controllers/Admin/LeaveRequestController.php: Controller Admin untuk edit/hapus data master cuti.

resources/views/admin/leave-requests/print_individual.blade.php: Template PDF surat izin perorangan.

resources/views/leave-requests/create.blade.php: Form pengajuan dengan Alpine.js.

Tabel Database Utama:

users: Menyimpan data pegawai, role, unit_kerja_id, atasan_id.

leave_types: Master jenis cuti (kuota, memerlukan_dokumen, bisa_retroaktif).

leave_requests: Transaksi pengajuan (status: pending, approved, rejected).

user_leave_quotas: Tracking sisa cuti per tahun.

holidays: Daftar hari libur nasional.

Konfigurasi Penting (.env):

MAIL_MAILER=smtp (Menggunakan Gmail).

MAIL_PASSWORD: Wajib menggunakan App Password 16 digit (bukan password login Gmail).

4. Permasalahan & Solusi
Masalah 1: Error 403 Forbidden saat Atasan melakukan Approval.

Penyebab: Penggunaan $this->authorize('update', ...) default Laravel memblokir user yang bukan pemilik data.

Solusi: Mengganti logic authorize dengan pengecekan manual di controller: if (Auth::id() !== $request->user->atasan_id) abort(403);.

Masalah 2: Error reason field is required saat mengajukan Izin Sakit.

Penyebab: Field reason di-hidden oleh Alpine.js, sehingga tidak terkirim, tapi validasi backend mewajibkannya.

Solusi: Melakukan $request->merge(['reason' => '...']) sebelum $request->validate().

Masalah 3: Queue Email Gagal di Hosting (PHP Version Mismatch).

Penyebab: CLI default server menggunakan PHP 8.1, sedangkan project butuh PHP 8.2 (Composer dependency).

Solusi: Menggunakan path absolut binary PHP 8.2 pada Cron Job.

Path Valid: /opt/alt/php82/usr/bin/php.

Masalah 4: Gmail SMTP Error (535 Username and Password not accepted).

Penyebab: Menggunakan password akun Google biasa.

Solusi: Generate App Password di pengaturan keamanan Google dan update .env, lalu php artisan config:clear.

5. Keputusan Teknis Penting
Mekanisme Queue di Shared Hosting:

Tidak menggunakan Supervisor (karena keterbatasan akses).

Menggunakan Cron Job yang berjalan Setiap Menit.

Perintah Cron: /opt/alt/php82/usr/bin/php [path_to_project]/artisan queue:work --stop-when-empty --tries=3 --backoff=10.

Flag --stop-when-empty wajib agar proses mati setelah selesai dan tidak membebani RAM server.

Pencetakan PDF:

Gambar Header/Footer dikonversi ke Base64 di Controller/View untuk menghindari error image not found pada DOMPDF di environment server tertentu.

Path Tanda tangan menggunakan storage_path (bukan asset URL).

Integritas Data:

Menggunakan DB::transaction dan lockForUpdate() pada tabel kuota saat store atau approve untuk mencegah kuota minus akibat double request.

6. Status Terakhir
Kondisi: Kode Controller (LeaveRequestController) sudah diperbarui dengan perbaikan validasi, transaksi database, dan logika approval manual.

Tugas Mendesak Selanjutnya:

Memastikan Cron Job dengan path /opt/alt/php82/usr/bin/php sudah berjalan dan email antrean terkirim.

Menguji coba alur pengajuan "Izin Sakit" (dengan upload file) untuk memastikan validasi baru berjalan mulus.

Menguji coba alur approval oleh Atasan.

7. Catatan Khusus
Asumsi: Tabel holidays sudah terisi data tanggal merah untuk perhitungan durasi cuti yang akurat.

Batasan: Karena menggunakan Shared Hosting, pengiriman email massal harus diberi jeda (delay/backoff) agar tidak terkena Rate Limit SMTP Gmail.

Dilarang Diubah: Logika pemanggilan binary PHP di Cron Job (/opt/alt/php82/...) jangan diubah kembali ke php standar, karena akan menyebabkan crash versi PHP.