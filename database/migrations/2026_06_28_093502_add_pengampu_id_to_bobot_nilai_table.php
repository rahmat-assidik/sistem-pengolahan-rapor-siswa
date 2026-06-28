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
        Schema::table('bobot_nilai', function (Blueprint $table) {
            $table->foreignId('pengampu_id')->nullable()->constrained('pengampu')->onDelete('cascade');
            $table->unique('pengampu_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bobot_nilai', function (Blueprint $table) {
            $table->dropForeign(['pengampu_id']);
            $table->dropUnique(['pengampu_id']);
            $table->dropColumn('pengampu_id');
        });
    }
};
