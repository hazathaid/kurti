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
        Schema::table('kurtis', function (Blueprint $table) {
            $table->string('bulan')->nullable()->after('pekan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kurtis', function (Blueprint $table) {
            $table->dropColumn('bulan');
        });
    }
};
