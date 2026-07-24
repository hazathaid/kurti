@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl py-4 sm:py-8">
    <a href="{{ route('weekly-reports.index') }}" class="mb-4 inline-flex items-center text-sm font-medium text-slate-600 hover:text-emerald-700">← Kembali</a>
    <div class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-widest text-emerald-600">Laporan Baru</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-900 sm:text-3xl">Buat Weekly Report</h1>
        <p class="mt-2 text-sm text-slate-500">Isi perkembangan murid. Setelah dikirim, orang tua akan mendapatkan notifikasi.</p>
    </div>

    <form method="POST" action="{{ route('weekly-reports.store') }}" class="space-y-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
        @csrf
        <div>
            <label for="murid_id" class="block text-sm font-medium text-gray-700 mb-1">Murid</label>
            <select id="murid_id" name="murid_id" required class="w-full rounded-xl border-gray-300 px-3 py-3 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">Pilih murid</option>
                @foreach($students as $student)
                    <option value="{{ $student->id }}" @selected(old('murid_id') == $student->id)>{{ $student->name }}</option>
                @endforeach
            </select>
            @error('murid_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="week_start" class="block text-sm font-medium text-gray-700 mb-1">Tanggal dalam pekan</label>
            <input id="week_start" type="date" name="week_start" value="{{ old('week_start', now()->startOfWeek()->toDateString()) }}" required class="w-full rounded-xl border-gray-300 px-3 py-3 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            <p class="text-xs text-gray-500 mt-1">Sistem akan menyimpan tanggal Senin pada pekan tersebut.</p>
            @error('week_start') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="summary" class="block text-sm font-medium text-gray-700 mb-1">Ringkasan perkembangan</label>
            <textarea id="summary" name="summary" rows="5" maxlength="5000" required class="w-full rounded-xl border-gray-300 px-3 py-3 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Ceritakan kegiatan dan perkembangan murid pekan ini...">{{ old('summary') }}</textarea>
            @error('summary') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="achievements" class="block text-sm font-medium text-gray-700 mb-1">Pencapaian (opsional)</label>
            <textarea id="achievements" name="achievements" rows="3" maxlength="5000" class="w-full rounded-xl border-gray-300 px-3 py-3 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Contoh: sudah berani tampil di depan kelas">{{ old('achievements') }}</textarea>
        </div>

        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan / tindak lanjut (opsional)</label>
            <textarea id="notes" name="notes" rows="3" maxlength="5000" class="w-full rounded-xl border-gray-300 px-3 py-3 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Hal yang perlu dilanjutkan atau diperhatikan">{{ old('notes') }}</textarea>
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
            <a href="{{ route('weekly-reports.index') }}" class="rounded-xl bg-slate-100 px-5 py-3 text-center font-semibold text-slate-700 transition hover:bg-slate-200">Batal</a>
            <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-3 font-semibold text-white shadow-md transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200">Kirim ke Orang Tua</button>
        </div>
    </form>
</div>
@endsection
