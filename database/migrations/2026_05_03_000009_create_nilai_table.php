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
        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_siswa_id')->constrained('riwayat_kelas_siswa')->onDelete('cascade');
            $table->foreignId('pengampu_id')->constrained('pengampu')->onDelete('cascade');
            
            $table->string('jenis_nilai'); // e.g., 'Tugas 1', 'UTS', 'UAS', 'Sikap'
            $table->decimal('skor', 5, 2)->default(0);
            
            $table->timestamps();

            // Di-comment agar tidak bentrok di SQLite saat migration berikutnya yang menambah komponen_nilai_id
            // $table->unique(['kelas_siswa_id', 'pengampu_id', 'jenis_nilai'], 'uq_nilai_siswa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai');
    }
};
