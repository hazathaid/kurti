<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $table = 'classrooms';
    protected $fillable = ['name','description'];

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'class_room_users',
            'classroom_id',
            'user_id'
        );
    }

    public function murids()
    {
        return $this->hasMany(User::class, 'current_classroom_id')->where('type', 'murid');
    }


    public function murid()
    {
        return $this->belongsToMany(
            User::class,
            'class_room_users',
            'classroom_id',
            'user_id'
        )->where('type', 'murid'); // filter hanya user dengan type murid
    }


}
