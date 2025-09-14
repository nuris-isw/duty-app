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
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('nama_cuti')->unique();
            $table->integer('kuota')->default(0);
            $table->enum('satuan', ['hari', 'kali'])->default('hari');
            $table->enum('periode_reset', ['tahunan', 'tidak_ada'])->default('tidak_ada');
            $table->boolean('memerlukan_dokumen')->default(false);
            $table->boolean('bisa_retroaktif')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
