<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function classrooms()
    {
        return $this->belongsToMany(
            ClassRoom::class,
            'class_room_users',
            'user_id',
            'classroom_id'
        );
    }

    public function kurtis()
    {
        return $this->hasMany(Kurti::class, 'murid_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function orangTua()
    {
        return $this->belongsToMany(User::class, 'murid_orang_tuas', 'murid_id', 'orangtua_id')
                    ->where('type', 'orangtua');
    }

    public function anak()
    {
        return $this->belongsToMany(User::class, 'murid_orang_tuas', 'orangtua_id', 'murid_id')
                    ->where('type', 'murid');
    }

    public function anakKurtis()
    {
        return Kurti::whereIn('murid_id', $this->anak()->pluck('users.id'));
    }

}
