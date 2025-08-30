<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kurti extends Model
{
    protected $fillable = [
        'murid_id',
        'pekan',
        'aktivitas',
        'capaian',
        'amanah_rumah',
        'catatan_orang_tua',
    ];

    public function classroom()
    {
        return $this->belongsTo(ClassRoom::class);
    }
    public function murid()
    {
        return $this->belongsTo(User::class, 'murid_id');
    }
    public function getStatusGroupedAttribute()
    {
        $kurtis = Kurti::where('murid_id', $this->murid_id)
            ->where('pekan', $this->pekan)
            ->get();

        if ($kurtis->isEmpty()) {
            return 'belum ada data';
        }

        $filledCount = $kurtis->whereNotNull('catatan_orang_tua')
                            ->where('catatan_orang_tua', '!=', '')
                            ->count();

        if ($filledCount === 0) {
            return 'belum diisi';
        }

        if ($filledCount < $kurtis->count()) {
            return 'onprogress';
        }

        return 'done';
    }

}
