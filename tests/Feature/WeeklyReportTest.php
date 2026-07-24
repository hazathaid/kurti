<?php

use App\Models\Classroom;
use App\Models\User;
use App\Models\WeeklyReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    Http::fake(['https://exp.host/*' => Http::response(['data' => []])]);

    $this->classroom = Classroom::create(['name' => 'Kelas Uji']);
    $this->fasil = User::factory()->create([
        'type' => 'fasil',
        'current_classroom_id' => $this->classroom->id,
    ]);
    $this->student = User::factory()->create([
        'type' => 'murid',
        'current_classroom_id' => $this->classroom->id,
    ]);
    $this->parent = User::factory()->create(['type' => 'orangtua']);
    $this->parent->anak()->attach($this->student);
});

it('allows a facilitator to create a weekly report for their student', function () {
    $response = $this->actingAs($this->fasil)->post('/weekly-reports', [
        'murid_id' => $this->student->id,
        'week_start' => '2026-07-22',
        'summary' => 'Perkembangan sangat baik.',
        'achievements' => 'Berani presentasi.',
    ]);

    $report = WeeklyReport::first();

    $response->assertRedirect(route('weekly-reports.show', $report));
    expect($report->week_start->toDateString())->toBe('2026-07-20');
    $this->assertDatabaseHas('weekly_reports', [
        'murid_id' => $this->student->id,
        'fasil_id' => $this->fasil->id,
        'summary' => 'Perkembangan sangat baik.',
    ]);
});

it('marks a report read when a linked parent opens it', function () {
    $report = WeeklyReport::create([
        'murid_id' => $this->student->id,
        'fasil_id' => $this->fasil->id,
        'classroom_id' => $this->classroom->id,
        'week_start' => '2026-07-20',
        'summary' => 'Laporan.',
    ]);

    $this->actingAs($this->parent)
        ->get(route('weekly-reports.show', $report))
        ->assertOk();

    expect($report->fresh()->read_at)->not->toBeNull()
        ->and($report->fresh()->read_by)->toBe($this->parent->id);
});

it('accepts optional parent feedback and exposes it to the facilitator', function () {
    $report = WeeklyReport::create([
        'murid_id' => $this->student->id,
        'fasil_id' => $this->fasil->id,
        'classroom_id' => $this->classroom->id,
        'week_start' => '2026-07-20',
        'summary' => 'Laporan.',
    ]);

    $this->actingAs($this->parent)
        ->put(route('weekly-reports.feedback', $report), [
            'parent_feedback' => 'Terima kasih, akan kami lanjutkan di rumah.',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('weekly_reports', [
        'id' => $report->id,
        'feedback_by' => $this->parent->id,
        'parent_feedback' => 'Terima kasih, akan kami lanjutkan di rumah.',
    ]);

    $this->actingAs($this->fasil)
        ->get(route('weekly-reports.show', $report))
        ->assertOk()
        ->assertSee('Terima kasih, akan kami lanjutkan di rumah.');
});

it('prevents unrelated parents and facilitators from viewing a report', function () {
    $report = WeeklyReport::create([
        'murid_id' => $this->student->id,
        'fasil_id' => $this->fasil->id,
        'classroom_id' => $this->classroom->id,
        'week_start' => '2026-07-20',
        'summary' => 'Rahasia.',
    ]);
    $otherParent = User::factory()->create(['type' => 'orangtua']);
    $otherFasil = User::factory()->create(['type' => 'fasil']);

    $this->actingAs($otherParent)->get(route('weekly-reports.show', $report))->assertForbidden();
    $this->actingAs($otherFasil)->get(route('weekly-reports.show', $report))->assertForbidden();
});
