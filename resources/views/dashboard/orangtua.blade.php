@extends('layouts.app')
@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <h2 class="text-2xl font-bold mb-6">Dashboard Orang Tua</h2>

@foreach($kurtis as $muridName => $pekanList)
    <div class="bg-white shadow-md rounded-xl p-6 mb-6">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">{{ $muridName }}</h3>

        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Pekan</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Tanggal</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Status</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($pekanList as $pekanGroup)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-600">
                            {{ $pekanGroup->pekan }}
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-600">
                            {{ $pekanGroup->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-4 py-2 text-sm">
                            @php
                                $status = $pekanGroup->items->first()->status_grouped;
                                $color = match($status) {
                                    'Done' => 'bg-green-100 text-green-800',
                                    'On Progress' => 'bg-yellow-100 text-yellow-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $color }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <a href="{{ route('kurtis.show', [
                                'murid' => $pekanGroup->items->first()->murid_id,
                                'pekan' => $pekanGroup->pekan
                            ]) }}"
                               class="text-blue-600 hover:underline">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endforeach
            </div>
    </div>
@endsection
