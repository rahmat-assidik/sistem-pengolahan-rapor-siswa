<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the old table completely
        Schema::dropIfExists('nilai');
        Schema::dropIfExists('komponen_nilai');

        // Recreate it with the new schema
        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_siswa_id')->constrained('riwayat_kelas_siswa')->onDelete('cascade');
            $table->foreignId('pengampu_id')->constrained('pengampu')->onDelete('cascade');
            $table->decimal('tugas', 5, 2)->nullable();
            $table->decimal('ulangan', 5, 2)->nullable();
            $table->decimal('uts', 5, 2)->nullable();
            $table->decimal('uas', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['kelas_siswa_id', 'pengampu_id'], 'uq_nilai_siswa_simple');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai');

        // Recreate komponen_nilai table
        Schema::create('komponen_nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengampu_id')->constrained('pengampu')->onDelete('cascade');
            $table->string('nama_komponen', 100);
            $table->enum('tipe', ['p_tugas', 'p_uh']);
            $table->timestamps();
        });

        // Recreate old nilai table
        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_siswa_id')->constrained('riwayat_kelas_siswa')->onDelete('cascade');
            $table->foreignId('pengampu_id')->constrained('pengampu')->onDelete('cascade');
            $table->string('jenis_nilai')->nullable();
            $table->decimal('skor', 5, 2)->default(0);
            $table->foreignId('komponen_nilai_id')->nullable()->constrained('komponen_nilai')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['kelas_siswa_id', 'pengampu_id', 'jenis_nilai', 'komponen_nilai_id'], 'uq_nilai_siswa_new');
        });
    }
};
