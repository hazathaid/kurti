<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classroom;
use App\Models\User;

class ClassroomSeeder extends Seeder
{
    public function run(): void
    {
        $fasil  = User::firstWhere('type', 'fasil');
        $murids = User::where('type', 'murid')->pluck('id')->all();

        $class = Classroom::firstOrCreate(
            ['name' => 'Kelas 1A'],
            ['description' => 'Kelas pertama dengan fasil guru']
        );

        if ($fasil) {
            $class->users()->syncWithoutDetaching([$fasil->id]);
        }

        if (!empty($murids)) {
            $class->users()->syncWithoutDetaching($murids);
        }
    }
}
