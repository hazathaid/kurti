@extends('layouts.app')
@section('content')
    <div class="page-shell">
        <div class="page-header">
            <div>
                <p class="page-eyebrow">Ringkasan Kelas</p>
                <h1 class="page-title">Rekap {{ $classroom->name }}</h1>
                <p class="page-description">Status pengisian Kurti seluruh murid berdasarkan bulan dan pekan.</p>
            </div>
            <a href="{{ route('kurtis.rekap.download') }}"
                class="btn-download">
                Download PDF
            </a>
        </div>
        <div class="surface p-4 sm:p-6">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Bulan</th>
                        <th>Pekan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($groupedByMurid as $murid)
                        @foreach ($murid->groups as $bulanGroup)
                            @foreach ($bulanGroup->pekans as $pekanGroup)
                                <tr>
                                    <td class="font-semibold text-slate-800">
                                        {{ $murid->murid_name }}
                                    </td>
                                    <td>
                                        {{ $bulanGroup->bulan }}
                                    </td>
                                    <td>
                                        Pekan {{ $pekanGroup->pekan }}
                                    </td>
                                    <td>
                                        @if($pekanGroup->status === 'Sudah isi')
                                            <span class="status-badge bg-emerald-100 text-emerald-700">
                                                {{ $pekanGroup->status }}
                                            </span>
                                        @elseif($pekanGroup->status === 'On progress')
                                            <span class="status-badge bg-amber-100 text-amber-700">
                                                {{ $pekanGroup->status }}
                                            </span>
                                        @else
                                            <span class="status-badge bg-rose-100 text-rose-700">
                                                {{ $pekanGroup->status }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
        </div>
        <div class="mt-5">
            <a href="{{ route('dashboard') }}"
            class="btn-secondary">
                ← Kembali
            </a>
        </div>
    </div>
@endsection
