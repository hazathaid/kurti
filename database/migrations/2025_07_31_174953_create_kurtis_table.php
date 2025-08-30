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
        Schema::create('kurtis', function (Blueprint $table) {
            $table->id();
            $table->string('pekan');
            $table->text('aktivitas')->nullable();
            $table->text('capaian')->nullable();
            $table->text('amanah_rumah')->nullable();
            $table->text('catatan_orang_tua')->nullable();

            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->foreignId('murid_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kurtis');
    }
};
