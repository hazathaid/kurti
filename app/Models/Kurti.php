<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ClassRoom;
use App\Models\User;
Use App\Models\KurtiGroup;

class Kurti extends Model
{
    protected $fillable = [
        'murid_id',
        'aktivitas',
        'capaian',
        'amanah_rumah',
        'catatan_orang_tua',
        'classroom_id',
        'kurti_group_id',
        'created_by',
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
            ->where('kurti_group_id', $this->kurti_group_id) // pakai group_id
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

    public function group()
    {
        return $this->belongsTo(KurtiGroup::class, 'kurti_group_id');
    }

}
