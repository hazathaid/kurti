@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Weekly Report</h1>
            <p class="text-sm text-gray-500 mt-1">Laporan perkembangan mingguan murid.</p>
        </div>
        @if(auth()->user()->type === 'fasil')
            <a href="{{ route('weekly-reports.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                + Buat Report
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="space-y-4">
        @forelse($reports as $report)
            <a href="{{ route('weekly-reports.show', $report) }}" class="block bg-white shadow-sm hover:shadow-md border border-gray-100 rounded-xl p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="font-semibold text-lg text-gray-900">{{ $report->murid->name }}</h2>
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
            <div class="bg-white rounded-xl border border-dashed p-10 text-center text-gray-500">
                Belum ada weekly report.
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $reports->links() }}</div>
</div>
@endsection
