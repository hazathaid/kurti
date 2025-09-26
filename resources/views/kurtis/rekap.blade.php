@extends('layouts.app')
@section('content')
    <div class="max-w-10xl mx-auto p-6 bg-white rounded-lg shadow">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Rekap Kelas {{ $classroom->name }}</h2>
            <a href="{{ route('kurtis.rekap.download') }}"
                class="bg-red-600 text-white rounded hover:bg-red-500 px-4 py-2">
                Download PDF
            </a>
        </div>
        <div class="overflow-x-auto mt-4">
            <table class="table-auto border-collapse border border-gray-400 w-full">
                <thead>
                    <tr>
                        <th class="border border-gray-400 px-2 py-1">Nama</th>
                        <th class="border border-gray-400 px-2 py-1">Bulan</th>
                        <th class="border border-gray-400 px-2 py-1">Pekan</th>
                        <th class="border border-gray-400 px-2 py-1">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($groupedByMurid as $murid)
                        @foreach ($murid->groups as $bulanGroup)
                            @foreach ($bulanGroup->pekans as $pekanGroup)
                                <tr>
                                    <td class="border border-gray-400 px-2 py-1">
                                        {{ $murid->murid_name }}
                                    </td>
                                    <td class="border border-gray-400 px-2 py-1">
                                        {{ $bulanGroup->bulan }}
                                    </td>
                                    <td class="border border-gray-400 px-2 py-1">
                                        Pekan {{ $pekanGroup->pekan }}
                                    </td>
                                    <td class="border border-gray-300 px-3 py-2">
                                        @if($pekanGroup->status === 'Sudah isi')
                                            <span class="text-green-600 font-semibold">
                                                {{ $pekanGroup->status }}
                                            </span>
                                        @elseif($pekanGroup->status === 'On progress')
                                            <span class="text-yellow-600 font-semibold">
                                                {{ $pekanGroup->status }}
                                            </span>
                                        @else
                                            <span class="text-red-600 font-semibold">
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
        <div class="mt-6">
            <a href="{{ route('dashboard') }}"
            class="inline-block px-4 py-2 bg-gray-200 hover:bg-gray-300 text-sm rounded-lg">
                ← Kembali
            </a>
        </div>
    </div>
@endsection
