@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Dashboard Fasil</h1>

    @if($classroom)
        <div class="mb-10 bg-white shadow rounded-lg">
            <div class="p-4 border-b">
                <h2 class="text-lg font-semibold">
                    Kurti di Kelas "{{ $classroom->name }}"
                </h2>
            </div>
            <div class="p-4 space-y-8">

                @foreach($groupedByMurid as $murid)
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-700">
                                {{ $murid->murid_name }}
                            </h3>
                            <a href="{{ route('kurtis.create', ['murid' => $murid->murid_id]) }}"
                               class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-3 py-2 rounded-lg shadow">
                                + Tambah Kurti
                            </a>
                        </div>

                        @if($murid->pekan->isEmpty())
                            <p class="text-gray-400 italic">Belum ada data kurti</p>
                        @else
                            <table class="w-full border border-gray-300 text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 border">Pekan</th>
                                        <th class="px-4 py-2 border">Tanggal</th>
                                        <th class="px-4 py-2 border">Status</th>
                                        <th class="px-4 py-2 border">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($murid->pekan as $pekanGroup)
                                        @php
                                            $latest = $pekanGroup->items->sortByDesc('created_at')->first();
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-2 border">{{ $pekanGroup->pekan }}</td>
                                            <td class="px-4 py-2 border">{{ $pekanGroup->created_at->format('d M Y') }}</td>
                                            <td class="px-4 py-2 border">
                                                @if(!$latest->catatan_orang_tua)
                                                    <span class="px-2 py-1 rounded bg-gray-200 text-gray-700 text-xs">Belum diisi</span>
                                                @elseif($latest->catatan_orang_tua && $latest->status !== 'done')
                                                    <span class="px-2 py-1 rounded bg-yellow-200 text-yellow-800 text-xs">On Progress</span>
                                                @else
                                                    <span class="px-2 py-1 rounded bg-green-200 text-green-800 text-xs">Done</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 border">
                                                <a href="{{ route('kurtis.show', ['murid' => $murid->murid_id, 'pekan' => $pekanGroup->pekan]) }}"
                                                   class="text-blue-500 hover:underline text-sm">
                                                    Lihat
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                @endforeach

            </div>
        </div>
    @else
        <p class="text-gray-400">Fasil belum terdaftar di kelas manapun.</p>
    @endif
</div>
@endsection
