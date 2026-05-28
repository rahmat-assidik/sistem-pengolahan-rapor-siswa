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
        Schema::create('pengampu', function (Blueprint $table) {
            $table->id();
            $table->string('guru_id', 20);
            $table->foreign('guru_id')->references('nip')->on('guru')->onDelete('cascade');
            $table->string('mapel_id', 20);
            $table->foreign('mapel_id')->references('kode_mapel')->on('mapel')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semester')->onDelete('cascade');
            $table->integer('kkm')->default(75);
            $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Aktif');
            $table->timestamps();

            $table->unique(['guru_id', 'mapel_id', 'kelas_id', 'semester_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengampu');
    }
};
