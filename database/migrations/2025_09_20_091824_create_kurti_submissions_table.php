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
        Schema::create('kurti_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('murid_id');
            $table->foreign('murid_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('kurti_group_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kurti_submissions');
    }
};
