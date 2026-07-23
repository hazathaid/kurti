<?php

namespace App\Services;

use App\Http\Controllers\Api\NotificationController;
use App\Models\Kurti;
use App\Models\KurtiReminder;
use Carbon\CarbonInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class KurtiReminderService
{
    public function __construct(
        private readonly NotificationController $notifications,
    ) {}

    public function sendForDate(CarbonInterface $date): int
    {
        $month = $date->format('Y-m');
        $week = min(5, (int) ceil($date->day / 7));
        $sent = 0;

        $pending = Kurti::query()
            ->select(['murid_id', 'kurti_group_id'])
            ->whereHas('group', fn ($query) => $query
                ->where('bulan', $month)
                ->where('pekan', $week))
            ->whereDoesntHave('group.submissions', fn ($query) => $query
                ->whereColumn('murid_id', 'kurtis.murid_id'))
            ->groupBy('murid_id', 'kurti_group_id')
            ->havingRaw(
                "SUM(CASE WHEN catatan_orang_tua IS NULL OR TRIM(catatan_orang_tua) = '' THEN 1 ELSE 0 END) > 0"
            )
            ->with(['murid.orangTua:id'])
            ->get();

        foreach ($pending as $item) {
            foreach ($item->murid->orangTua as $parent) {
                $reminder = $this->claimReminder(
                    $parent->id,
                    $item->murid_id,
                    $item->kurti_group_id,
                    $date,
                );

                if (! $reminder) {
                    continue;
                }

                $delivered = $this->notifications->sendToUsers(
                    [$parent->id],
                    'Kurti hari ini belum diisi',
                    "Masih ada aktivitas Kurti {$item->murid->name} yang perlu diisi.",
                    [
                        'muridId' => $item->murid_id,
                        'groupId' => $item->kurti_group_id,
                    ],
                );

                if ($delivered > 0) {
                    $reminder->update(['sent_at' => now()]);
                    $sent++;
                } else {
                    $reminder->delete();
                }
            }
        }

        Log::info('Kurti daily reminder completed', [
            'date' => $date->toDateString(),
            'pending_groups' => $pending->count(),
            'sent' => $sent,
        ]);

        return $sent;
    }

    private function claimReminder(
        int $userId,
        int $muridId,
        int $groupId,
        CarbonInterface $date,
    ): ?KurtiReminder {
        try {
            return KurtiReminder::create([
                'user_id' => $userId,
                'murid_id' => $muridId,
                'kurti_group_id' => $groupId,
                'reminder_date' => $date->toDateString(),
            ]);
        } catch (QueryException $exception) {
            if (in_array($exception->errorInfo[0] ?? null, ['23000', '23505'], true)) {
                return null;
            }

            throw $exception;
        }
    }
}
