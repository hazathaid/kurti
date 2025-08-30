<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kurti;
use App\Models\User;
use App\Models\ClassRoom;

class KurtiSeeder extends Seeder
{
    public function run(): void
    {
        $murid = User::where('type', 'murid')->first();
        $class = ClassRoom::first();
        $fasil  = User::firstWhere('type', 'fasil');

        Kurti::create([
            'pekan' => 'Minggu 1',
            'murid_id' => $murid->id,
            'classroom_id' => $class->id,
            'created_by' => $fasil->id,
            'aktivitas' => 'Menghafal Juz 30',
            'capaian' => 'Sudah hafal An-Naba sampai An-Naziat',
            'amanah_rumah' => 'Sholat berjamaah 5 waktu',
            'catatan_orang_tua' => null,
        ]);
    }
}
