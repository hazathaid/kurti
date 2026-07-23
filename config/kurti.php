<?php

return [
    'reminder' => [
        'enabled' => env('KURTI_REMINDER_ENABLED', true),
        'time' => env('KURTI_REMINDER_TIME', '18:00'),
        'timezone' => env('KURTI_REMINDER_TIMEZONE', 'Asia/Jakarta'),
    ],
];
