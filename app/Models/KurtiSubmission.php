<?php

namespace App\Models;
use App\Models\User;
use App\Models\KurtiGroup;
use Illuminate\Database\Eloquent\Model;

class KurtiSubmission extends Model
{
    protected $fillable = ['murid_id', 'kurti_group_id', 'file_path'];

    public function murid()
    {
        return $this->belongsTo(User::class, 'murid_id');
    }

    public function group()
    {
        return $this->belongsTo(KurtiGroup::class, 'kurti_group_id');
    }

}
