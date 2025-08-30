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

}
