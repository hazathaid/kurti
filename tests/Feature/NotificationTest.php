<?php

use App\Models\Classroom;
use App\Models\Kurti;
use App\Models\KurtiGroup;
use App\Models\User;
use App\Models\UserDevices;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;

function notificationUser(string $type, array $attributes = []): User
{
    return User::factory()->create(array_merge(['type' => $type], $attributes));
}

function notificationKurti(User $child, Classroom $classroom, User $creator): Kurti
{
    $group = KurtiGroup::create(['bulan' => '2026-07', 'pekan' => 1]);

    return Kurti::create([
        'murid_id' => $child->id,
        'classroom_id' => $classroom->id,
        'kurti_group_id' => $group->id,
        'created_by' => $creator->id,
        'aktivitas' => 'Membaca',
    ]);
}

test('creating kurti notifies only linked parents with detail identifiers', function () {
    Http::fake(['https://exp.host/*' => Http::response([
        'data' => [['status' => 'ok', 'id' => 'ticket-1']],
    ])]);

    $classroom = Classroom::create(['name' => 'Kelas A']);
    $facilitator = notificationUser('fasil', ['current_classroom_id' => $classroom->id]);
    $child = notificationUser('murid', ['current_classroom_id' => $classroom->id]);
    $parent = notificationUser('orangtua');
    $unrelatedParent = notificationUser('orangtua');
    $parent->anak()->attach($child);
    UserDevices::create(['user_id' => $parent->id, 'fcm_token' => 'ExpoPushToken[parent]']);
    UserDevices::create(['user_id' => $unrelatedParent->id, 'fcm_token' => 'ExpoPushToken[other]']);
    Sanctum::actingAs($facilitator);

    $this->postJson('/api/kurtis', [
        'murid_id' => $child->id,
        'classroom_id' => $classroom->id,
        'kurtis' => [[
            'bulan' => '2026-07',
            'pekan' => 1,
            'aktivitas' => 'Membaca',
        ]],
    ])->assertOk();

    $groupId = Kurti::first()->kurti_group_id;
    Http::assertSent(fn (Request $request) =>
        $request[0]['to'] === 'ExpoPushToken[parent]'
        && $request[0]['data']['muridId'] === $child->id
        && $request[0]['data']['groupId'] === $groupId
        && count($request->data()) === 1
    );
});

test('saving a parent note notifies the kurti creator', function () {
    Http::fake(['https://exp.host/*' => Http::response([
        'data' => [['status' => 'ok', 'id' => 'ticket-1']],
    ])]);

    $classroom = Classroom::create(['name' => 'Kelas A']);
    $facilitator = notificationUser('fasil', ['current_classroom_id' => $classroom->id]);
    $child = notificationUser('murid', ['current_classroom_id' => $classroom->id]);
    $parent = notificationUser('orangtua');
    $parent->anak()->attach($child);
    $kurti = notificationKurti($child, $classroom, $facilitator);
    UserDevices::create(['user_id' => $facilitator->id, 'fcm_token' => 'ExpoPushToken[facilitator]']);
    Sanctum::actingAs($parent);

    $this->putJson("/api/kurtis/{$kurti->id}/catatan", [
        'catatan_orangtua' => 'Sudah dilakukan',
    ])->assertOk();

    Http::assertSent(fn (Request $request) =>
        $request[0]['to'] === 'ExpoPushToken[facilitator]'
        && $request[0]['data']['muridId'] === $child->id
        && $request[0]['data']['groupId'] === $kurti->kurti_group_id
    );
});

test('invalid Expo device tokens are removed without failing the main request', function () {
    Http::fake(['https://exp.host/*' => Http::response([
        'data' => [[
            'status' => 'error',
            'details' => ['error' => 'DeviceNotRegistered'],
        ]],
    ])]);

    $classroom = Classroom::create(['name' => 'Kelas A']);
    $facilitator = notificationUser('fasil', ['current_classroom_id' => $classroom->id]);
    $child = notificationUser('murid', ['current_classroom_id' => $classroom->id]);
    $parent = notificationUser('orangtua');
    $parent->anak()->attach($child);
    UserDevices::create(['user_id' => $parent->id, 'fcm_token' => 'ExpoPushToken[expired]']);
    Sanctum::actingAs($facilitator);

    $this->postJson('/api/kurtis', [
        'murid_id' => $child->id,
        'classroom_id' => $classroom->id,
        'kurtis' => [[
            'bulan' => '2026-07',
            'pekan' => 1,
            'aktivitas' => 'Membaca',
        ]],
    ])->assertOk();

    $this->assertDatabaseMissing('user_devices', ['fcm_token' => 'ExpoPushToken[expired]']);
});
