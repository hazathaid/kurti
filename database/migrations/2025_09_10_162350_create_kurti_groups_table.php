<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kurti_groups', function (Blueprint $table) {
            $table->id();
            $table->string('bulan');
            $table->integer('pekan');
            $table->timestamps();
        });

        Schema::table('kurtis', function (Blueprint $table) {
            // hapus kolom bulan & pekan kalau sudah ada
            if (Schema::hasColumn('kurtis', 'bulan')) {
                $table->dropColumn('bulan');
            }
            if (Schema::hasColumn('kurtis', 'pekan')) {
                $table->dropColumn('pekan');
            }

            // tambahkan relasi ke kurti_groups
            $table->foreignId('kurti_group_id')
                ->nullable()
                ->constrained('kurti_groups')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('kurtis', function (Blueprint $table) {
            $table->dropForeign(['kurti_group_id']);
            $table->dropColumn('kurti_group_id');

            // rollback, tambahkan lagi kolom bulan & pekan
            $table->string('bulan')->nullable();
            $table->integer('pekan')->nullable();
        });

        Schema::dropIfExists('kurti_groups');
    }
};
