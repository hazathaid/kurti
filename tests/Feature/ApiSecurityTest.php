<?php

use App\Models\Classroom;
use App\Models\Kurti;
use App\Models\KurtiGroup;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

function apiUser(string $type, array $attributes = []): User
{
    return User::factory()->create(array_merge(['type' => $type], $attributes));
}

function apiKurti(User $murid, Classroom $classroom, User $creator): Kurti
{
    $group = KurtiGroup::create(['bulan' => '2026-07', 'pekan' => 1]);

    return Kurti::create([
        'murid_id' => $murid->id,
        'classroom_id' => $classroom->id,
        'kurti_group_id' => $group->id,
        'created_by' => $creator->id,
        'aktivitas' => 'Membaca',
    ]);
}

test('login succeeds with valid credentials', function () {
    $user = apiUser('orangtua', [
        'email' => 'parent@example.com',
        'password' => 'secret-password',
    ]);

    $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'secret-password',
    ])->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('user.id', $user->id)
        ->assertJsonStructure(['token']);

    $this->assertDatabaseCount('personal_access_tokens', 1);
});

test('login fails with invalid credentials', function () {
    $user = apiUser('orangtua', ['password' => 'correct-password']);

    $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertUnauthorized()
        ->assertJson(['message' => 'Invalid credentials']);

    $this->assertDatabaseCount('personal_access_tokens', 0);
});

test('logout revokes the current token', function () {
    $user = apiUser('orangtua');
    $token = $user->createToken('mobile-app');

    $this->withToken($token->plainTextToken)
        ->postJson('/api/logout')
        ->assertOk()
        ->assertJson(['message' => 'Logged out']);

    $this->assertDatabaseMissing('personal_access_tokens', [
        'id' => $token->accessToken->id,
    ]);
});

test('dashboard is available to parents and facilitators', function (string $type) {
    $classroom = Classroom::create(['name' => 'Kelas A']);
    $user = apiUser($type, $type === 'fasil' ? ['current_classroom_id' => $classroom->id] : []);
    Sanctum::actingAs($user);

    $this->getJson('/api/dashboard')
        ->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonStructure(['data']);
})->with(['orangtua', 'fasil']);

test('facilitator dashboard data is not wrapped in a second response envelope', function () {
    $classroom = Classroom::create(['name' => 'Kelas QA']);
    $facilitator = apiUser('fasil', ['current_classroom_id' => $classroom->id]);
    $child = apiUser('murid', ['current_classroom_id' => $classroom->id]);
    apiKurti($child, $classroom, $facilitator);
    Sanctum::actingAs($facilitator);

    $this->getJson('/api/dashboard')
        ->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.0.murid_id', $child->id)
        ->assertJsonMissingPath('data.status')
        ->assertJsonMissingPath('data.data');
});

test('parent cannot read another parents child kurti', function () {
    $classroom = Classroom::create(['name' => 'Kelas A']);
    $facilitator = apiUser('fasil', ['current_classroom_id' => $classroom->id]);
    $child = apiUser('murid', ['current_classroom_id' => $classroom->id]);
    $kurti = apiKurti($child, $classroom, $facilitator);
    Sanctum::actingAs(apiUser('orangtua'));

    $this->getJson("/api/kurtis/{$child->id}/{$kurti->kurti_group_id}")
        ->assertForbidden();
});

test('parent cannot update another parents child kurti', function () {
    $classroom = Classroom::create(['name' => 'Kelas A']);
    $facilitator = apiUser('fasil', ['current_classroom_id' => $classroom->id]);
    $child = apiUser('murid', ['current_classroom_id' => $classroom->id]);
    $kurti = apiKurti($child, $classroom, $facilitator);
    Sanctum::actingAs(apiUser('orangtua'));

    $this->putJson("/api/kurtis/{$kurti->id}/catatan", [
        'catatan_orangtua' => 'Tidak boleh tersimpan',
    ])->assertForbidden();

    expect($kurti->fresh()->catatan_orang_tua)->toBeNull();
});

test('parent can read linked child detail and saved note persists after reload', function () {
    $classroom = Classroom::create(['name' => 'Kelas A']);
    $facilitator = apiUser('fasil', ['current_classroom_id' => $classroom->id]);
    $parent = apiUser('orangtua');
    $child = apiUser('murid', ['current_classroom_id' => $classroom->id]);
    $parent->anak()->attach($child->id);
    $kurti = apiKurti($child, $classroom, $facilitator);
    Sanctum::actingAs($parent);

    $this->getJson("/api/kurtis/{$child->id}/{$kurti->kurti_group_id}")
        ->assertOk()
        ->assertJsonPath('group.kurtis.0.id', $kurti->id)
        ->assertJsonPath('group.kurtis.0.catatan_orangtua', null);

    $this->putJson("/api/kurtis/{$kurti->id}/catatan", [
        'catatan_orangtua' => 'Sudah dipraktikkan di rumah',
    ])->assertOk()
        ->assertJsonPath('data.catatan_orang_tua', 'Sudah dipraktikkan di rumah');

    $this->getJson("/api/kurtis/{$child->id}/{$kurti->kurti_group_id}")
        ->assertOk()
        ->assertJsonPath(
            'group.kurtis.0.catatan_orangtua',
            'Sudah dipraktikkan di rumah'
        );
});

test('facilitator can read active classroom detail but not a mismatched group', function () {
    $classroom = Classroom::create(['name' => 'Kelas A']);
    $facilitator = apiUser('fasil', ['current_classroom_id' => $classroom->id]);
    $child = apiUser('murid', ['current_classroom_id' => $classroom->id]);
    $kurti = apiKurti($child, $classroom, $facilitator);
    $unrelatedGroup = KurtiGroup::create(['bulan' => '2026-08', 'pekan' => 2]);
    Sanctum::actingAs($facilitator);

    $this->getJson("/api/kurtis/{$child->id}/{$kurti->kurti_group_id}")
        ->assertOk()
        ->assertJsonPath('group.kurtis.0.id', $kurti->id);

    $this->getJson("/api/kurtis/{$child->id}/{$unrelatedGroup->id}")
        ->assertNotFound();
});

test('facilitator cannot create kurti for another classroom', function () {
    $ownClassroom = Classroom::create(['name' => 'Kelas A']);
    $otherClassroom = Classroom::create(['name' => 'Kelas B']);
    $facilitator = apiUser('fasil', ['current_classroom_id' => $ownClassroom->id]);
    $child = apiUser('murid', ['current_classroom_id' => $otherClassroom->id]);
    Sanctum::actingAs($facilitator);

    $this->postJson('/api/kurtis', [
        'murid_id' => $child->id,
        'classroom_id' => $otherClassroom->id,
        'kurtis' => [[
            'bulan' => '2026-07',
            'pekan' => 1,
            'aktivitas' => 'Membaca',
        ]],
    ])->assertForbidden();

    $this->assertDatabaseCount('kurtis', 0);
});

test('every item is validated when creating multiple kurtis', function () {
    $classroom = Classroom::create(['name' => 'Kelas A']);
    $facilitator = apiUser('fasil', ['current_classroom_id' => $classroom->id]);
    $child = apiUser('murid', ['current_classroom_id' => $classroom->id]);
    Sanctum::actingAs($facilitator);

    $this->postJson('/api/kurtis', [
        'murid_id' => $child->id,
        'classroom_id' => $classroom->id,
        'kurtis' => [
            ['bulan' => '2026-07', 'pekan' => 1, 'aktivitas' => 'Membaca'],
            ['bulan' => 'July 2026', 'aktivitas' => 'Menulis'],
        ],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['kurtis.1.bulan', 'kurtis.1.pekan']);

    $this->assertDatabaseCount('kurtis', 0);
});
