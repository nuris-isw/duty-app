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
        // 1. Tabel Mesin Fingerprint
        Schema::create('finger_machines', function (Blueprint $table) {
            $table->id(); // Integer Auto Increment
            $table->string('machine_name', 100)->nullable();
            $table->string('sn', 50)->unique(); // Serial Number Unik
            $table->string('ip_address', 20)->nullable();
            $table->string('model_name', 50)->nullable();
            $table->timestamp('created_at')->useCurrent();
            // Note: Updated_at opsional, tidak ada di screenshot jadi tidak saya masukkan wajib
        });

        // 2. Tabel User pada Mesin (id_absensfinger)
        Schema::create('id_absensfinger', function (Blueprint $table) {
            $table->id(); // BigInt Auto Increment
            
            // Kolom relasi ke Tabel Users Laravel (PENTING)
            // Nullable karena data dari mesin mungkin belum di-mapping ke pegawai
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->integer('user_id_machine')->unique(); // ID User di Mesin (misal: 1, 2, 105)
            $table->string('name', 100);
            $table->string('fingerprint_id', 50)->nullable();
            $table->string('status', 20)->default('active')->nullable();
            
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        // 3. Tabel Log Absensi Mentah (raw_attendance_logs)
        Schema::create('raw_attendance_logs', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke id_absensfinger berdasarkan user_id_machine
            // Kita index agar pencarian cepat
            $table->integer('user_id_machine')->index();
            
            // Hapus UNIQUE di sini agar Budi dan Ani bisa absen di detik yang sama
            $table->dateTime('timestamp'); 
            
            $table->string('machine_ip', 20);
            $table->string('machine_sn', 50)->nullable();
            
            $table->tinyInteger('punch')->nullable(); // 0: Masuk, 1: Pulang, dst (tergantung mesin)
            $table->tinyInteger('status')->nullable();
            
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_attendance_logs');
        Schema::dropIfExists('id_absensfinger');
        Schema::dropIfExists('finger_machines');
    }
};