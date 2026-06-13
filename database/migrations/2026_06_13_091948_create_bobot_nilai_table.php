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
        Schema::create('bobot_nilai', function (Blueprint $table) {
            $table->id();
            $table->integer('bobot_tugas')->default(30);
            $table->integer('bobot_ulangan')->default(20);
            $table->integer('bobot_uts')->default(25);
            $table->integer('bobot_uas')->default(25);
            $table->timestamps();
        });

        // Insert default values
        DB::table('bobot_nilai')->insert([
            'bobot_tugas' => 30,
            'bobot_ulangan' => 20,
            'bobot_uts' => 25,
            'bobot_uas' => 25,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bobot_nilai');
    }
};
