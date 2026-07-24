@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl py-4 sm:py-8">
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
        <div class="flex flex-wrap justify-between gap-3 border-b pb-5">
            <div>
                <p class="text-sm text-gray-500">Weekly Report</p>
                <h1 class="text-2xl font-bold">{{ $report->murid->name }}</h1>
                <p class="text-gray-500 mt-1">
                    {{ $report->week_start->format('d M') }}–{{ $report->week_start->copy()->endOfWeek()->format('d M Y') }}
                    · oleh {{ $report->fasil->name }}
                </p>
            </div>
            <div>
                @if($report->read_at)
                    <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                        Dibaca {{ $report->read_at->format('d M Y, H:i') }}
                    </span>
                @else
                    <span class="inline-flex rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">Belum dibaca orang tua</span>
                @endif
            </div>
        </div>

        <section class="py-5">
            <h2 class="font-semibold text-gray-900 mb-2">Ringkasan perkembangan</h2>
            <p class="text-gray-700 whitespace-pre-line">{{ $report->summary }}</p>
        </section>

        @if($report->achievements)
            <section class="py-5 border-t">
                <h2 class="font-semibold text-gray-900 mb-2">Pencapaian</h2>
                <p class="text-gray-700 whitespace-pre-line">{{ $report->achievements }}</p>
            </section>
        @endif

        @if($report->notes)
            <section class="py-5 border-t">
                <h2 class="font-semibold text-gray-900 mb-2">Catatan / tindak lanjut</h2>
                <p class="text-gray-700 whitespace-pre-line">{{ $report->notes }}</p>
            </section>
        @endif

        <section class="py-5 border-t">
            <h2 class="font-semibold text-gray-900 mb-2">Feedback orang tua <span class="font-normal text-gray-500">(opsional)</span></h2>
            @if(auth()->user()->type === 'orangtua')
                <form method="POST" action="{{ route('weekly-reports.feedback', $report) }}">
                    @csrf
                    @method('PUT')
                    <textarea name="parent_feedback" rows="4" maxlength="5000" class="w-full rounded-xl border-gray-300 px-3 py-3 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Tulis tanggapan atau pertanyaan jika ada...">{{ old('parent_feedback', $report->parent_feedback) }}</textarea>
                    @error('parent_feedback') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    <button class="mt-3 w-full rounded-xl bg-emerald-600 px-5 py-3 font-semibold text-white shadow-sm transition hover:bg-emerald-700 sm:w-auto">Simpan Feedback</button>
                </form>
            @else
                <p class="text-gray-700 whitespace-pre-line">{{ $report->parent_feedback ?: 'Belum ada feedback.' }}</p>
                @if($report->feedback_at)
                    <p class="text-xs text-gray-500 mt-2">{{ $report->feedback_at->format('d M Y, H:i') }}</p>
                @endif
            @endif
        </section>
    </div>

    <a href="{{ route('weekly-reports.index') }}" class="mt-5 inline-block font-medium text-emerald-700 hover:underline">← Kembali ke Weekly Report</a>
</div>
@endsection
