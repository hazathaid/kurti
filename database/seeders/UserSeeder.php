<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Classroom;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Classroom
        $class = Classroom::firstOrCreate(
            ['name' => 'Kelas 1A'],
            ['description' => 'Kelas pertama dengan fasil guru']
        );
        // Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'type' => 'administrator',
        ]);

        // Fasil
        $fasil = User::create([
            'name' => 'Fasil Guru',
            'email' => 'fasil@example.com',
            'password' => Hash::make('password'),
            'type' => 'fasil',
            'current_classroom_id' => $class->id
        ]);

        // Orangtua
        $ortu = User::create([
            'name' => 'Bapak Murid',
            'email' => 'ortu@example.com',
            'password' => Hash::make('password'),
            'type' => 'orangtua',
        ]);

        // Murid
        $murid1 = User::create([
            'name' => 'Murid Satu',
            'email' => 'murid1@example.com',
            'password' => Hash::make('password'),
            'type' => 'murid',
            'current_classroom_id' => $class->id
        ]);

        $murid2 = User::create([
            'name' => 'Murid Dua',
            'email' => 'murid2@example.com',
            'password' => Hash::make('password'),
            'type' => 'murid',
            'current_classroom_id' => $class->id
        ]);

        // Hubungan ortu <-> anak
        $ortu->anak()->attach([$murid1->id, $murid2->id]);
    }
}
