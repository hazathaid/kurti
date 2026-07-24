@extends('layouts.app')
@section('content')
    <div class="page-shell">
        <div class="page-header">
            <div>
                <p class="page-eyebrow">Pendampingan di Rumah</p>
                <h1 class="page-title">Dashboard Orang Tua</h1>
                <p class="page-description">Ikuti aktivitas, capaian, dan amanah rumah anak dari waktu ke waktu.</p>
            </div>
        </div>

        @forelse($kurtis as $muridName => $bulanList)
        <section class="surface mb-6 overflow-hidden">
            <div class="surface-header">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 font-bold text-emerald-700">{{ mb_substr($muridName, 0, 1) }}</div>
                    <h2 class="text-xl font-bold text-slate-900">{{ $muridName }}</h2>
                </div>
            </div>
            <div class="surface-body space-y-6">

            @foreach($bulanList as $bulan => $pekanList)
                <div>
                <h3 class="mb-2 text-sm font-bold text-slate-700">{{ $bulan }}</h3>

                <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Pekan</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pekanList as $pekanGroup)
                            <tr>
                                <td class="font-semibold text-slate-800">
                                    Pekan {{ $pekanGroup->pekan }}
                                </td>
                                <td>
                                    @php
                                        $status = $pekanGroup->items->first()->status_grouped;
                                        $color = match($status) {
                                            'Done' => 'bg-green-100 text-green-800',
                                            'On Progress' => 'bg-yellow-100 text-yellow-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="status-badge {{ $color }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('kurtis.show', [
                                        'murid' => $pekanGroup->items->first()->murid_id,
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
                </div>
            @endforeach
            </div>
        </section>
        @empty
            <div class="empty-state">
                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-xl">📚</div>
                <h2 class="font-bold text-slate-900">Belum ada data Kurti</h2>
                <p class="mt-1 text-sm text-slate-500">Aktivitas anak akan tampil setelah fasil menambahkannya.</p>
            </div>
        @endforelse
    </div>
@endsection
