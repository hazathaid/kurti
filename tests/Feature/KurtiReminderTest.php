<?php

use App\Models\Classroom;
use App\Models\Kurti;
use App\Models\KurtiGroup;
use App\Models\KurtiReminder;
use App\Models\User;
use App\Models\UserDevices;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

function reminderFixture(?string $note = null): array
{
    $classroom = Classroom::create(['name' => 'Kelas Reminder']);
    $facilitator = User::factory()->create([
        'type' => 'fasil',
        'current_classroom_id' => $classroom->id,
    ]);
    $parent = User::factory()->create(['type' => 'orangtua']);
    $child = User::factory()->create([
        'type' => 'murid',
        'current_classroom_id' => $classroom->id,
    ]);
    $parent->anak()->attach($child);
    $group = KurtiGroup::create(['bulan' => '2026-07', 'pekan' => 1]);
    $kurti = Kurti::create([
        'murid_id' => $child->id,
        'classroom_id' => $classroom->id,
        'kurti_group_id' => $group->id,
        'created_by' => $facilitator->id,
        'aktivitas' => 'Membaca',
        'catatan_orang_tua' => $note,
    ]);
    UserDevices::create([
        'user_id' => $parent->id,
        'fcm_token' => 'ExpoPushToken[reminder-parent]',
    ]);

    return compact('parent', 'child', 'group', 'kurti');
}

beforeEach(function () {
    Http::fake(['https://exp.host/*' => Http::response([
        'data' => [['status' => 'ok', 'id' => 'ticket-reminder']],
    ])]);
});

test('pending Kurti sends a daily reminder with the correct detail route', function () {
    $fixture = reminderFixture();
    $unrelatedParent = User::factory()->create(['type' => 'orangtua']);
    UserDevices::create([
        'user_id' => $unrelatedParent->id,
        'fcm_token' => 'ExpoPushToken[unrelated-parent]',
    ]);

    $this->artisan('kurti:send-reminders', ['--date' => '2026-07-03'])
        ->expectsOutput('Sent 1 Kurti reminder(s).')
        ->assertSuccessful();

    Http::assertSent(fn (Request $request) => $request[0]['to'] === 'ExpoPushToken[reminder-parent]'
        && $request[0]['data']['muridId'] === $fixture['child']->id
        && $request[0]['data']['groupId'] === $fixture['group']->id
        && count($request->data()) === 1
    );
    $this->assertDatabaseHas('kurti_reminders', [
        'user_id' => $fixture['parent']->id,
        'murid_id' => $fixture['child']->id,
        'kurti_group_id' => $fixture['group']->id,
        'reminder_date' => '2026-07-03 00:00:00',
    ]);
});

test('completed Kurti does not send a reminder', function () {
    reminderFixture('Sudah dilakukan di rumah');

    $this->artisan('kurti:send-reminders', ['--date' => '2026-07-03'])
        ->expectsOutput('Sent 0 Kurti reminder(s).')
        ->assertSuccessful();

    Http::assertNothingSent();
    $this->assertDatabaseCount('kurti_reminders', 0);
});

test('the same reminder is not sent twice on one day', function () {
    reminderFixture();

    $this->artisan('kurti:send-reminders', ['--date' => '2026-07-03'])->assertSuccessful();
    $this->artisan('kurti:send-reminders', ['--date' => '2026-07-03'])
        ->expectsOutput('Sent 0 Kurti reminder(s).')
        ->assertSuccessful();

    Http::assertSentCount(1);
    expect(KurtiReminder::count())->toBe(1);
});

test('a pending Kurti can be reminded again on the following day', function () {
    reminderFixture();

    $this->artisan('kurti:send-reminders', ['--date' => '2026-07-03'])->assertSuccessful();
    $this->artisan('kurti:send-reminders', ['--date' => '2026-07-04'])
        ->expectsOutput('Sent 1 Kurti reminder(s).')
        ->assertSuccessful();

    Http::assertSentCount(2);
    expect(KurtiReminder::count())->toBe(2);
});
