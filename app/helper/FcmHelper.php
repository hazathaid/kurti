<?php
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

if (!function_exists('send_fcm')) {
    function send_fcm($token, $title, $body, $data = [])
    {
        $messaging = app('firebase.messaging');

        $message = CloudMessage::withTarget('token', $token)
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

        return $messaging->send($message);
    }
}
