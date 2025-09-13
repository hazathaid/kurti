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
                            <h2 class="text-xl font-semibold mb-4">{{ $murid->murid_name }}</h2>
                            <a href="{{ route('kurtis.create', ['murid' => $murid->murid_id]) }}"
                               class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-3 py-2 rounded-lg shadow">
                                + Tambah Kurti
                            </a>
                        </div>
                        @foreach($murid->groups as $bulanGroup)
                            <h3 class="text-md font-medium text-gray-700 mt-4 mb-2">
                                Bulan: {{ \Carbon\Carbon::parse($bulanGroup->bulan . '-01')->format('F Y') }}
                            </h3>

                            <div class="overflow-x-auto">
                                <table class="w-full border text-sm">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-2 border">Pekan</th>
                                            <th class="px-4 py-2 border">Jumlah Aktivitas</th>
                                            <th class="px-4 py-2 border">Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bulanGroup->pekans as $pekanGroup)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-2 border">{{ $pekanGroup->pekan }}</td>
                                                <td class="px-4 py-2 border">{{ $pekanGroup->items->count() }}</td>
                                                <td class="px-4 py-2 border">
                                                    <a href="{{ route('kurtis.show', [
                                                        'murid' => $murid->murid_id,
                                                        'group' => $pekanGroup->group_id
                                                    ]) }}"
                                                    class="text-blue-500 hover:underline text-sm">
                                                        Lihat
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>
                @endforeach

            </div>
        </div>
    @else
        <p class="text-gray-400">Fasil belum terdaftar di kelas manapun.</p>
    @endif
</div>
@endsection
