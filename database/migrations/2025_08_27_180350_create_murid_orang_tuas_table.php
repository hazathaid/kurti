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
        Schema::create('murid_orang_tuas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('murid_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('orangtua_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('murid_orang_tuas');
    }
};
