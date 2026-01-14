<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendance_correction_requests', function (Blueprint $table) {
            $table->id();
            
            // Siapa yang mengajukan
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Tanggal yang ingin dikoreksi
            $table->date('date');
            
            // Data Usulan (Nullable: karena mungkin cuma koreksi jam pulang saja, atau jam masuk saja)
            $table->time('proposed_start_time')->nullable(); // Jam Masuk Usulan
            $table->time('proposed_end_time')->nullable();   // Jam Pulang Usulan
            
            // Alasan & Bukti
            $table->text('reason');
            $table->string('dokumen_pendukung')->nullable(); // Foto/PDF bukti error/surat tugas
            
            // Status Approval
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users'); // Siapa yang menyetujui (Atasan)
            $table->dateTime('approved_at')->nullable();
            $table->string('rejection_reason')->nullable(); // Alasan penolakan jika ada

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_correction_requests');
    }
};
