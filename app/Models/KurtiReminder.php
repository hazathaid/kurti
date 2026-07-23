<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KurtiReminder extends Model
{
    protected $fillable = [
        'user_id',
        'murid_id',
        'kurti_group_id',
        'reminder_date',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'reminder_date' => 'date',
            'sent_at' => 'datetime',
        ];
    }
}
