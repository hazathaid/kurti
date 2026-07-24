@extends('layouts.app')

@section('content')
<div class="page-shell">
    <div class="page-header">
        <div>
            <p class="page-eyebrow">Ruang Fasilitator</p>
            <h1 class="page-title">Dashboard Fasil</h1>
            <p class="page-description">Pantau dan dokumentasikan aktivitas belajar setiap murid dalam kelas aktif.</p>
        </div>
        @if($classroom)
            <a href="{{ route('rekap.kurti') }}" class="btn-download">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m4 6V7m4 10v-3M5 21h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2Z"/></svg>
                Lihat Rekap
            </a>
        @endif
    </div>

    @if($classroom)
        <div class="surface">
            <div class="surface-header">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Kelas aktif</p>
                    <h2 class="mt-1 text-xl font-bold text-slate-900">{{ $classroom->name }}</h2>
                </div>
                <span class="status-badge bg-emerald-100 text-emerald-700">{{ $groupedByMurid->count() }} murid</span>
            </div>
            <div class="divide-y divide-slate-100">

                @foreach($groupedByMurid as $murid)
                    <section class="p-5 sm:p-6">
                        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 font-bold text-emerald-700">{{ mb_substr($murid->murid_name, 0, 1) }}</div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $murid->murid_name }}</h2>
                            </div>
                            <a href="{{ route('kurtis.create', ['murid' => $murid->murid_id]) }}"
                               class="btn-primary w-full sm:w-auto">
                                + Tambah Kurti
                            </a>
                        </div>
                        @foreach($murid->groups as $bulanGroup)
                            <h3 class="mb-2 mt-5 text-sm font-bold text-slate-700">
                                {{ \Carbon\Carbon::parse($bulanGroup->bulan . '-01')->translatedFormat('F Y') }}
                            </h3>

                            <div class="table-wrap">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Pekan</th>
                                            <th>Jumlah Aktivitas</th>
                                            <th class="text-right">Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bulanGroup->pekans as $pekanGroup)
                                            <tr>
                                                <td class="font-semibold text-slate-800">Pekan {{ $pekanGroup->pekan }}</td>
                                                <td>{{ $pekanGroup->items->count() }} aktivitas</td>
                                                <td class="text-right">
                                                    <a href="{{ route('kurtis.show', [
                                                        'murid' => $murid->murid_id,
                                                        'group' => $pekanGroup->group_id
                                                    ]) }}"
                                                    class="font-semibold text-emerald-700 hover:text-emerald-800">
                                                        Lihat detail →
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </section>
                @endforeach

            </div>
        </div>
    @else
        <div class="empty-state">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-xl">🏫</div>
            <h2 class="font-bold text-slate-900">Belum ada kelas aktif</h2>
            <p class="mt-1 text-sm text-slate-500">Akun fasil belum ditugaskan ke kelas manapun.</p>
        </div>
    @endif
</div>
@endsection
