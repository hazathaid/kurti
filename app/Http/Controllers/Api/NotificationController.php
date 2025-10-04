<?php
namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function sendExpoPush($expoToken, $title, $body)
    {
        $response = Http::post('https://exp.host/--/api/v2/push/send', [
            'to' => $expoToken, // ExponentPushToken[...]
            'title' => $title,
            'body' => $body,
            'sound' => 'default', // optional
            'data' => [ 'extra' => 'value' ] // optional
        ]);

        return $response->json();
    }
}
