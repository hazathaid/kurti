@extends('layouts.app')

@section('content')
<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <h2 class="text-2xl font-bold mb-6">Dashboard Orang Tua</h2>

        @forelse($kurtis as $muridName => $pekanGroup)
            <div class="bg-white shadow-md rounded-xl p-6 mb-6">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">{{ $muridName }}</h3>

                @foreach($pekanGroup as $pekan => $items)
                    <div class="mb-4">
                        <h4 class="text-lg font-medium text-blue-600">Pekan {{ $pekan }}</h4>

                        <div class="overflow-x-auto mt-2">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Status</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($items as $item)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-600">
                                                {{ $item->status_grouped }}
                                            </td>
                                            <td class="px-4 py-2">
                                                <a href="{{ route('kurtis.show', ['murid' => $item->murid_id, 'pekan' => $item->pekan]) }}">
                                                    Lihat Detail
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
        @empty
            <p class="text-gray-500">Belum ada data kurti untuk ditampilkan.</p>
        @endforelse
    </div>
</x-app-layout>
