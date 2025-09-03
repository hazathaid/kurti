<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // hapus classroom_id di users
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'classroom_id')) {
                $table->dropForeign(['classroom_id']);
                $table->dropColumn('classroom_id');
            }
        });
    }

    public function down(): void
    {
        // rollback classroom_id ke users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'classroom_id')) {
                $table->unsignedBigInteger('classroom_id')->nullable();
                $table->foreign('classroom_id')->references('id')->on('classrooms');
            }
        });
    }
};
