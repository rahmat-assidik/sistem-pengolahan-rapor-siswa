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
        // Kolom catatan_wali sudah ada, skip jika sudah ada
        if (!Schema::hasColumn('riwayat_kelas_siswa', 'catatan_wali')) {
            Schema::table('riwayat_kelas_siswa', function (Blueprint $table) {
                $table->text('catatan_wali')->nullable()->after('semester_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_kelas_siswa', function (Blueprint $table) {
            $table->dropColumn('catatan_wali');
        });
    }
};
