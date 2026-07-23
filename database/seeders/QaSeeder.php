<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Kurti;
use App\Models\KurtiGroup;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class QaSeeder extends Seeder
{
    public const PASSWORD = 'Kurti-QA-2026!';

    public function run(): void
    {
        if (! app()->environment(['local', 'testing']) || DB::getDriverName() !== 'sqlite') {
            throw new RuntimeException(
                'QaSeeder hanya boleh dijalankan pada environment local/testing dengan database SQLite.'
            );
        }

        DB::transaction(function (): void {
            $classroom = Classroom::updateOrCreate(
                ['name' => 'Kelas QA Lokal'],
                ['description' => 'Data uji lokal untuk QA aplikasi Kurti']
            );

            $facilitator = $this->upsertUser(
                'qa.fasil@kurti.local',
                'Fasilitator QA',
                'fasil',
                $classroom->id
            );
            $parent = $this->upsertUser(
                'qa.ortu@kurti.local',
                'Orang Tua QA',
                'orangtua'
            );
            $otherParent = $this->upsertUser(
                'qa.ortu.lain@kurti.local',
                'Orang Tua Lain QA',
                'orangtua'
            );
            $child = $this->upsertUser(
                'qa.murid@kurti.local',
                'Murid QA',
                'murid',
                $classroom->id
            );
            $otherChild = $this->upsertUser(
                'qa.murid.lain@kurti.local',
                'Murid Lain QA',
                'murid',
                $classroom->id
            );

            $classroom->users()->syncWithoutDetaching([
                $facilitator->id,
                $child->id,
                $otherChild->id,
            ]);
            $parent->anak()->sync([$child->id]);
            $otherParent->anak()->sync([$otherChild->id]);

            $group = KurtiGroup::firstOrCreate([
                'bulan' => now()->format('Y-m'),
                'pekan' => 1,
            ]);

            Kurti::updateOrCreate(
                [
                    'kurti_group_id' => $group->id,
                    'murid_id' => $child->id,
                    'aktivitas' => 'Merapikan tempat tidur',
                ],
                [
                    'classroom_id' => $classroom->id,
                    'created_by' => $facilitator->id,
                    'amanah_rumah' => 'Lakukan setiap pagi',
                    'capaian' => 'Mampu melakukan dengan arahan',
                    'catatan_orang_tua' => null,
                ]
            );

            Kurti::updateOrCreate(
                [
                    'kurti_group_id' => $group->id,
                    'murid_id' => $child->id,
                    'aktivitas' => 'Membaca buku 15 menit',
                ],
                [
                    'classroom_id' => $classroom->id,
                    'created_by' => $facilitator->id,
                    'amanah_rumah' => null,
                    'capaian' => 'Menyelesaikan satu cerita pendek',
                    'catatan_orang_tua' => 'Sudah dilakukan bersama.',
                ]
            );

            $otherGroup = KurtiGroup::firstOrCreate([
                'bulan' => now()->format('Y-m'),
                'pekan' => 2,
            ]);

            Kurti::updateOrCreate(
                [
                    'kurti_group_id' => $otherGroup->id,
                    'murid_id' => $otherChild->id,
                    'aktivitas' => 'Data privat murid lain',
                ],
                [
                    'classroom_id' => $classroom->id,
                    'created_by' => $facilitator->id,
                    'amanah_rumah' => null,
                    'capaian' => null,
                    'catatan_orang_tua' => null,
                ]
            );
        });
    }

    private function upsertUser(
        string $email,
        string $name,
        string $type,
        ?int $classroomId = null
    ): User {
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make(self::PASSWORD),
                'type' => $type,
            ]
        );
        $user->current_classroom_id = $classroomId;
        $user->save();

        return $user;
    }
}
