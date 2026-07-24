@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl py-4 sm:py-8">
    <a href="{{ route('dashboard') }}" class="mb-4 inline-flex text-sm font-semibold text-slate-600 hover:text-emerald-700">← Kembali</a>
    <div class="page-header">
        <div>
            <p class="page-eyebrow">Perbarui Aktivitas</p>
            <h1 class="page-title">Edit Data Kurti</h1>
            <p class="page-description">Sesuaikan informasi aktivitas dan capaian murid.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('kurtis.update', $kurti->id) }}" class="surface p-5 sm:p-7">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div>
                <label class="field-label">Bulan</label>
                <input type="month" name="bulan" value="{{ old('bulan', $kurti->group->bulan) }}"
                       class="field-control">
            </div>
            <div>
                <label class="field-label">Pekan</label>
                <select name="pekan" class="field-control">
                    <option value="1" {{ old('pekan', $kurti->group->pekan) == 1 ? 'selected' : '' }}>Pekan 1</option>
                    <option value="2" {{ old('pekan', $kurti->group->pekan) == 2 ? 'selected' : '' }}>Pekan 2</option>
                    <option value="3" {{ old('pekan', $kurti->group->pekan) == 3 ? 'selected' : '' }}>Pekan 3</option>
                    <option value="4" {{ old('pekan', $kurti->group->pekan) == 4 ? 'selected' : '' }}>Pekan 4</option>
                </select>
            </div>
            <div>
                <label class="field-label">Aktivitas</label>
                <textarea name="aktivitas" rows="4" class="field-control">{{ old('aktivitas', $kurti->aktivitas) }}</textarea>
            </div>

            <div>
                <label class="field-label">Amanah Rumah</label>
                <textarea name="amanah_rumah" rows="4" class="field-control">{{ old('amanah_rumah', $kurti->amanah_rumah) }}</textarea>
            </div>

            <div>
                <label class="field-label">Capaian</label>
                <textarea name="capaian" rows="4" class="field-control">{{ old('capaian', $kurti->capaian) }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
            <a href="{{ route('dashboard') }}"
               class="btn-secondary">
                Batal
            </a>
            <button type="submit"
                    class="btn-primary">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
