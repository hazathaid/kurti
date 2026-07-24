@extends('layouts.app')

@section('content')
<div class="page-shell">
    <div class="page-header">
        <div>
            <p class="page-eyebrow">Dokumentasi Aktivitas</p>
            <h1 class="page-title">Detail Kurti — {{ $murid->name }}</h1>
            <p class="page-description">
                {{ \Carbon\Carbon::parse($group->bulan . '-01')->translatedFormat('F Y') }} · Pekan {{ $group->pekan }}
            </p>
        </div>
        <a href="{{ route('kurti.download.pdf', ['murid' => $murid->id, 'group' => $group->id]) }}" class="btn-download">
            Download PDF
        </a>
    </div>

    <div class="surface p-4 sm:p-6">
    <div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Aktivitas</th>
                <th>Capaian</th>
                <th>Amanah Rumah</th>
                <th>Catatan Orang Tua</th>
                @if($user->type === 'fasil')
                    <th>Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($kurtis as $kurti)
                <tr>
                    <td class="min-w-48 font-medium text-slate-800">
                        {{ $kurti->aktivitas }}
                    </td>
                    <td class="min-w-48">
                        {{ $kurti->capaian }}
                    </td>
                    <td class="min-w-48">
                        {{ $kurti->amanah_rumah }}
                    </td>
                    <td class="min-w-64">
                        @if($user->type === 'orangtua')
                            <form action="{{ route('kurtis.updateCatatan', $kurti->id) }}" method="POST" class="flex flex-col gap-2">
                                @csrf
                                @method('PUT')
                                <textarea name="catatan_orang_tua" class="field-control min-h-20 resize-none text-sm" placeholder="Tulis catatan..." oninput="this.style.height='auto'; this.style.height=this.scrollHeight+'px'">{{ $kurti->catatan_orang_tua }}</textarea>
                                <button type="submit"
                                        class="btn-primary self-end">
                                    Simpan
                                </button>
                            </form>
                        @else
                            {{ $kurti->catatan_orang_tua ?? '-' }}
                        @endif
                    </td>
                    @if($user->type === 'fasil')
                        <td class="whitespace-nowrap">
                            <div class="flex items-center gap-3">
                            <a href="{{ route('kurtis.edit', $kurti->id) }}"
                               class="text-sm font-semibold text-emerald-700 hover:underline">Edit</a>
                            <form action="{{ route('kurtis.destroy', $kurti->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Yakin ingin menghapus aktivitas {{ $kurti->aktivitas }} untuk {{ $murid->name }}?')"
                                        class="text-sm font-semibold text-rose-600 hover:underline">
                                    Hapus
                                </button>
                            </form>
                            </div>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    </div>
    @php
        $submission = $group->latestSubmissionForMurid($murid->id);
    @endphp

    @if($submission)
        <div class="surface mt-6 p-5 sm:p-6">
        <div class="flex items-center gap-2 text-sm font-semibold text-emerald-700"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Foto sudah diunggah · {{ $submission->created_at->format('d M Y') }}</div>
        <img src="{{ asset('storage/' . $submission->file_path) }}" alt="Foto Kurti" class="mt-4 max-h-[36rem] w-full rounded-xl border border-slate-200 object-contain">
        </div>
    @else
        <form action="{{ route('kurti-submissions.store') }}" method="POST" enctype="multipart/form-data" class="surface mt-6 p-5 sm:p-6">
            @csrf
            <input type="hidden" name="murid_id" value="{{ $murid->id }}">
            <input type="hidden" name="kurti_group_id" value="{{ $group->id }}">

            <label class="field-label">Upload Foto Kurti</label>
            <p class="mb-3 text-sm text-slate-500">Tambahkan bukti dokumentasi aktivitas untuk pekan ini.</p>
            <input type="file" name="file_path" required class="field-control text-sm">
            <button type="submit" class="btn-primary mt-4">
                Upload
            </button>
        </form>
    @endif

    <div class="mt-6">
        <a href="{{ route('dashboard') }}"
           class="btn-secondary">
            ← Kembali
        </a>
    </div>
</div>
@endsection
