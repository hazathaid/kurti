<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WeeklyReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class WeeklyReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        abort_unless(in_array($user->type, ['fasil', 'orangtua'], true), 403);

        $query = WeeklyReport::with(['murid:id,name', 'fasil:id,name', 'reader:id,name', 'feedbackAuthor:id,name']);
        $user->type === 'fasil'
            ? $query->where('fasil_id', $user->id)
            : $query->whereIn('murid_id', $user->anak()->select('users.id'));

        return response()->json(['status' => 'success', 'data' => $query->orderByDesc('week_start')->get()]);
    }

    public function students()
    {
        $user = Auth::user();
        abort_unless($user->type === 'fasil' && $user->current_classroom_id, 403);

        return response()->json([
            'status' => 'success',
            'data' => User::where('type', 'murid')
                ->where('current_classroom_id', $user->current_classroom_id)
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->type === 'fasil' && $user->current_classroom_id, 403);

        $validated = $request->validate([
            'murid_id' => ['required', 'integer', 'exists:users,id'],
            'week_start' => ['required', 'date'],
            'summary' => ['required', 'string', 'max:5000'],
            'achievements' => ['nullable', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $student = User::whereKey($validated['murid_id'])
            ->where('type', 'murid')
            ->where('current_classroom_id', $user->current_classroom_id)
            ->firstOrFail();

        $weekStart = Carbon::parse($validated['week_start'])->startOfWeek()->toDateString();
        if (WeeklyReport::where('murid_id', $student->id)->whereDate('week_start', $weekStart)->exists()) {
            throw ValidationException::withMessages([
                'week_start' => 'Weekly report untuk murid dan pekan ini sudah ada.',
            ]);
        }

        $report = WeeklyReport::create([
            ...$validated,
            'week_start' => $weekStart,
            'fasil_id' => $user->id,
            'classroom_id' => $user->current_classroom_id,
        ]);

        app(NotificationController::class)->sendToUsers(
            $student->orangTua()->pluck('users.id')->all(),
            'Weekly report baru',
            "Laporan mingguan {$student->name} sudah tersedia.",
            ['weeklyReportId' => $report->id],
        );

        return response()->json(['status' => 'success', 'data' => $report->load('murid:id,name')], 201);
    }

    public function show(WeeklyReport $weeklyReport)
    {
        $user = Auth::user();
        $this->authorizeVisible($weeklyReport, $user);

        if ($user->type === 'orangtua' && $weeklyReport->read_at === null) {
            $weeklyReport->update(['read_at' => now(), 'read_by' => $user->id]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $weeklyReport->load(['murid:id,name', 'fasil:id,name', 'reader:id,name', 'feedbackAuthor:id,name']),
        ]);
    }

    public function feedback(Request $request, WeeklyReport $weeklyReport)
    {
        $user = Auth::user();
        abort_unless($user->type === 'orangtua', 403);
        $this->authorizeVisible($weeklyReport, $user);

        $validated = $request->validate([
            'parent_feedback' => ['present', 'nullable', 'string', 'max:5000'],
        ]);
        $hasFeedback = filled($validated['parent_feedback']);

        $weeklyReport->update([
            'parent_feedback' => $validated['parent_feedback'],
            'feedback_at' => $hasFeedback ? now() : null,
            'feedback_by' => $hasFeedback ? $user->id : null,
        ]);

        if ($hasFeedback) {
            app(NotificationController::class)->sendToUsers(
                [$weeklyReport->fasil_id],
                'Feedback weekly report',
                "Orang tua {$weeklyReport->murid->name} memberikan feedback.",
                ['weeklyReportId' => $weeklyReport->id],
            );
        }

        return response()->json(['status' => 'success', 'data' => $weeklyReport->fresh()]);
    }

    private function authorizeVisible(WeeklyReport $report, User $user): void
    {
        $visible = $user->type === 'fasil'
            ? $report->fasil_id === $user->id
            : ($user->type === 'orangtua' && $user->anak()->whereKey($report->murid_id)->exists());
        abort_unless($visible, 403);
    }
}
