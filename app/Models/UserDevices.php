<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDevices extends Model
{
    protected $fillable = [
        'user_id',
        'fcm_token'
    ];

}
