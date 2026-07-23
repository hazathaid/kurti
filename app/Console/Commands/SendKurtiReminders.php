<?php

namespace App\Console\Commands;

use App\Services\KurtiReminderService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendKurtiReminders extends Command
{
    protected $signature = 'kurti:send-reminders {--date= : Date to process (Y-m-d)}';

    protected $description = 'Send daily reminders for current Kurti groups that are not complete';

    public function handle(KurtiReminderService $reminders): int
    {
        $timezone = config('kurti.reminder.timezone');
        $date = $this->option('date')
            ? Carbon::createFromFormat('Y-m-d', $this->option('date'), $timezone)->startOfDay()
            : Carbon::now($timezone)->startOfDay();

        $sent = $reminders->sendForDate($date);
        $this->info("Sent {$sent} Kurti reminder(s).");

        return self::SUCCESS;
    }
}
