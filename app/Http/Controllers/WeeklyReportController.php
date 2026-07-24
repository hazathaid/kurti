<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\NotificationController;
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

        $reports = $this->visibleReports($user)
            ->with(['murid', 'fasil', 'reader', 'feedbackAuthor'])
            ->orderByDesc('week_start')
            ->paginate(20);

        return view('weekly-reports.index', compact('reports'));
    }

    public function create()
    {
        $user = Auth::user();
        abort_unless($user->type === 'fasil' && $user->current_classroom_id, 403);

        $students = User::query()
            ->where('type', 'murid')
            ->where('current_classroom_id', $user->current_classroom_id)
            ->orderBy('name')
            ->get();

        return view('weekly-reports.create', compact('students'));
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

        $student = User::query()
            ->whereKey($validated['murid_id'])
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

        return redirect()->route('weekly-reports.show', $report)
            ->with('success', 'Weekly report berhasil dikirim ke orang tua.');
    }

    public function show(WeeklyReport $weeklyReport)
    {
        $user = Auth::user();
        $this->authorizeVisible($weeklyReport, $user);
        $this->markAsRead($weeklyReport, $user);

        return view('weekly-reports.show', [
            'report' => $weeklyReport->load(['murid', 'fasil', 'reader', 'feedbackAuthor']),
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

        $weeklyReport->update([
            'parent_feedback' => $validated['parent_feedback'],
            'feedback_at' => filled($validated['parent_feedback']) ? now() : null,
            'feedback_by' => filled($validated['parent_feedback']) ? $user->id : null,
        ]);

        if (filled($validated['parent_feedback'])) {
            app(NotificationController::class)->sendToUsers(
                [$weeklyReport->fasil_id],
                'Feedback weekly report',
                "Orang tua {$weeklyReport->murid->name} memberikan feedback.",
                ['weeklyReportId' => $weeklyReport->id],
            );
        }

        return back()->with('success', 'Feedback berhasil disimpan.');
    }

    private function visibleReports(User $user)
    {
        $query = WeeklyReport::query();

        return $user->type === 'fasil'
            ? $query->where('fasil_id', $user->id)
            : $query->whereIn('murid_id', $user->anak()->select('users.id'));
    }

    private function authorizeVisible(WeeklyReport $report, User $user): void
    {
        $visible = $user->type === 'fasil'
            ? $report->fasil_id === $user->id
            : ($user->type === 'orangtua' && $user->anak()->whereKey($report->murid_id)->exists());

        abort_unless($visible, 403);
    }

    private function markAsRead(WeeklyReport $report, User $user): void
    {
        if ($user->type === 'orangtua' && $report->read_at === null) {
            $report->update(['read_at' => now(), 'read_by' => $user->id]);
        }
    }
}
