<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kurti_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('murid_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kurti_group_id')->constrained()->cascadeOnDelete();
            $table->date('reminder_date');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['user_id', 'murid_id', 'kurti_group_id', 'reminder_date'],
                'kurti_reminders_daily_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kurti_reminders');
    }
};
