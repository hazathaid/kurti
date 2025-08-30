@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Detail Kurti</h2>

    @if($kurti->isNotEmpty())
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="bg-green-500 text-white px-4 py-2 font-semibold">
                {{ $kurti->first()->murid->name }} - {{ $kurti->first()->pekan }}
            </div>

            <div class="p-6">
                <table class="min-w-full border border-gray-200 rounded-lg">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 border">Aktivitas</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 border">Capaian</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 border">Amanah Rumah</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 border">Catatan Orang Tua</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($kurti as $row)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-700 border">{{ $row->aktivitas }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700 border">{{ $row->capaian }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700 border">{{ $row->amanah_rumah }}</td>
                                <td class="px-4 py-2 border">
                                    <form action="{{ route('kurtis.updateCatatan', $row->id) }}" method="POST" class="flex gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="text"
                                               name="catatan_orang_tua"
                                               value="{{ $row->catatan_orang_tua }}"
                                               class="flex-1 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none"
                                               placeholder="Isi catatan...">
                                        <button type="submit"
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm">
                                            Simpan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-6">
                    <a href="{{ route('kurtis.index') }}"
                       class="inline-block bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                       ← Kembali
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="bg-yellow-100 text-yellow-800 px-4 py-3 rounded-lg">
            Tidak ada data Kurti untuk pekan ini.
        </div>
        <div class="mt-4">
            <a href="{{ route('kurtis.index') }}"
               class="inline-block bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
               ← Kembali
            </a>
        </div>
    @endif
</div>
@endsection
