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
        Schema::table('nilai', function (Blueprint $table) {
            // Nonaktifkan foreign key check sementara
            Schema::disableForeignKeyConstraints();
            
            // Hapus index unik lama jika ada
            try {
                DB::statement('ALTER TABLE nilai DROP INDEX uq_nilai_siswa');
            } catch (\Exception $e) {
                // Index sudah dihapus
            }
            
            // Aktifkan kembali foreign key check
            Schema::enableForeignKeyConstraints();
            
            // Tambahkan index unik baru yang mencakup komponen_nilai_id
            $table->unique(['kelas_siswa_id', 'pengampu_id', 'jenis_nilai', 'komponen_nilai_id'], 'uq_nilai_siswa_new');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            Schema::disableForeignKeyConstraints();
            
            try {
                DB::statement('ALTER TABLE nilai DROP INDEX uq_nilai_siswa_new');
            } catch (\Exception $e) {
                // Index sudah dihapus
            }
            
            try {
                $table->unique(['kelas_siswa_id', 'pengampu_id', 'jenis_nilai'], 'uq_nilai_siswa');
            } catch (\Exception $e) {
                // Index sudah ada
            }
            
            Schema::enableForeignKeyConstraints();
        });
    }
};
