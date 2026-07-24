@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-6">Buat Weekly Report</h1>

    <form method="POST" action="{{ route('weekly-reports.store') }}" class="bg-white rounded-xl shadow-sm p-6 space-y-5">
        @csrf
        <div>
            <label for="murid_id" class="block text-sm font-medium text-gray-700 mb-1">Murid</label>
            <select id="murid_id" name="murid_id" required class="w-full rounded-lg border-gray-300">
                <option value="">Pilih murid</option>
                @foreach($students as $student)
                    <option value="{{ $student->id }}" @selected(old('murid_id') == $student->id)>{{ $student->name }}</option>
                @endforeach
            </select>
            @error('murid_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="week_start" class="block text-sm font-medium text-gray-700 mb-1">Tanggal dalam pekan</label>
            <input id="week_start" type="date" name="week_start" value="{{ old('week_start', now()->startOfWeek()->toDateString()) }}" required class="w-full rounded-lg border-gray-300">
            <p class="text-xs text-gray-500 mt-1">Sistem akan menyimpan tanggal Senin pada pekan tersebut.</p>
            @error('week_start') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="summary" class="block text-sm font-medium text-gray-700 mb-1">Ringkasan perkembangan</label>
            <textarea id="summary" name="summary" rows="5" maxlength="5000" required class="w-full rounded-lg border-gray-300" placeholder="Ceritakan kegiatan dan perkembangan murid pekan ini...">{{ old('summary') }}</textarea>
            @error('summary') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="achievements" class="block text-sm font-medium text-gray-700 mb-1">Pencapaian (opsional)</label>
            <textarea id="achievements" name="achievements" rows="3" maxlength="5000" class="w-full rounded-lg border-gray-300">{{ old('achievements') }}</textarea>
        </div>

        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan / tindak lanjut (opsional)</label>
            <textarea id="notes" name="notes" rows="3" maxlength="5000" class="w-full rounded-lg border-gray-300">{{ old('notes') }}</textarea>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('weekly-reports.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700">Batal</a>
            <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white">Kirim ke Orang Tua</button>
        </div>
    </form>
</div>
@endsection
