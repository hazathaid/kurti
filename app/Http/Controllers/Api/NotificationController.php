<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDevices;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function sendToUsers(array $userIds, string $title, string $body, array $data): void
    {
        try {
            $tokens = UserDevices::query()
                ->whereIn('user_id', array_unique($userIds))
                ->pluck('fcm_token')
                ->unique()
                ->values();

            foreach ($tokens->chunk(100) as $tokenChunk) {
                $messages = $tokenChunk->map(fn ($token) => [
                    'to' => $token,
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                    'data' => $data,
                ])->values()->all();

                $response = Http::post('https://exp.host/--/api/v2/push/send', $messages);

                if (! $response->successful()) {
                    Log::warning('Expo push request failed', ['status' => $response->status()]);
                    continue;
                }

                $tickets = $response->json('data', []);
                $tickets = isset($tickets['status']) ? [$tickets] : $tickets;

                foreach ($tickets as $index => $ticket) {
                    if (($ticket['details']['error'] ?? null) === 'DeviceNotRegistered') {
                        UserDevices::where('fcm_token', $tokenChunk->values()->get($index))->delete();
                    }
                }
            }
        } catch (\Throwable $exception) {
            Log::warning('Expo push notification could not be sent', [
                'exception' => $exception::class,
            ]);
        }
    }
}
