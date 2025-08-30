<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kurti extends Model
{
    public function classroom()
    {
        return $this->belongsTo(ClassRoom::class);
    }
    public function murid()
    {
        return $this->belongsTo(User::class, 'murid_id');
    }
}
