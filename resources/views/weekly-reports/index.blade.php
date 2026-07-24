@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-6xl py-4 sm:py-8">
    <div class="mb-6 flex flex-col gap-4 rounded-2xl bg-gradient-to-r from-slate-900 to-slate-700 p-5 text-white shadow-lg sm:flex-row sm:items-center sm:justify-between sm:p-7">
        <div>
            <p class="mb-1 text-xs font-semibold uppercase tracking-widest text-emerald-300">Perkembangan Murid</p>
            <h1 class="text-2xl font-bold sm:text-3xl">Weekly Report</h1>
            <p class="mt-2 text-sm text-slate-300">Laporan perkembangan mingguan murid untuk fasil dan orang tua.</p>
        </div>
        @if(auth()->user()->type === 'fasil' && auth()->user()->current_classroom_id)
            <a href="{{ route('weekly-reports.create') }}" class="inline-flex w-full shrink-0 items-center justify-center gap-2 rounded-xl bg-emerald-500 px-5 py-3 font-semibold text-white shadow-md transition hover:-translate-y-0.5 hover:bg-emerald-400 focus:outline-none focus:ring-4 focus:ring-emerald-300 sm:w-auto">
                <span class="text-xl leading-none">+</span> Buat Report
            </a>
        @endif
    </div>

    @if(auth()->user()->type === 'fasil' && ! auth()->user()->current_classroom_id)
        <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 shadow-sm">
            Akun fasil ini belum punya kelas aktif. Weekly report baru bisa dibuat setelah kelas di-assign ke akun ini.
        </div>
    @endif

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="space-y-4">
        @forelse($reports as $report)
            <a href="{{ route('weekly-reports.show', $report) }}" class="group block rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-md">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 transition group-hover:text-emerald-700">{{ $report->murid->name }}</h2>
                        <p class="text-sm text-gray-500">
                            Pekan {{ $report->week_start->format('d M') }}–{{ $report->week_start->copy()->endOfWeek()->format('d M Y') }}
                        </p>
                        <p class="text-gray-700 mt-3 line-clamp-2">{{ $report->summary }}</p>
                    </div>
                    <div class="text-right">
                        @if($report->read_at)
                            <span class="inline-flex rounded-full bg-green-100 text-green-700 text-xs font-medium px-3 py-1">Sudah dibaca</span>
                        @else
                            <span class="inline-flex rounded-full bg-yellow-100 text-yellow-700 text-xs font-medium px-3 py-1">Belum dibaca</span>
                        @endif
                        @if($report->parent_feedback)
                            <div class="text-xs text-blue-600 mt-2">Ada feedback</div>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-white p-10 text-center shadow-sm">
                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-2xl">📝</div>
                <h2 class="font-semibold text-slate-800">Belum ada weekly report</h2>
                <p class="mt-1 text-sm text-slate-500">Report yang dibuat akan tampil di halaman ini.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $reports->links() }}</div>
</div>
@endsection
