@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow-md rounded-xl p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-semibold text-gray-800 px-4 py-2">
            Detail Kurti - {{ $murid->name }}
        </h2>
        <a href="{{ route('kurti.download.pdf', ['murid' => $murid->id, 'group' => $group->id]) }}" class="bg-red-600 text-white rounded hover:bg-red-700 px-4 py-2">
            Download PDF
        </a>
    </div>
    <p class="mb-4 text-gray-600">
        Bulan: {{ \Carbon\Carbon::parse($group->bulan . '-01')->format('F Y') }},
        Pekan: {{ $group->pekan }}
    </p>

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Aktifitas</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Capaian</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Amanah Rumah</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Catatan Orang Tua</th>
                @if($user->type === 'fasil')
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($kurtis as $kurti)
                <tr>
                    <td class="px-4 py-2 text-sm text-gray-600 border">
                        {{ $kurti->aktivitas }}
                    </td>
                    <td class="px-4 py-2 text-sm border">
                        {{ $kurti->capaian }}
                    </td>
                    <td class="px-4 py-2 text-sm border">
                        {{ $kurti->amanah_rumah }}
                    </td>
                    <td class="px-4 py-2 border">
                        @if($user->type === 'orangtua')
                            <form action="{{ route('kurtis.updateCatatan', $kurti->id) }}" method="POST" class="flex gap-2">
                                @csrf
                                @method('PUT')
                                <input type="text"
                                    name="catatan_orang_tua"
                                    value="{{ $kurti->catatan_orang_tua }}"
                                    class="flex-1 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none"
                                    placeholder="Isi catatan...">
                                <button type="submit"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm">
                                    Simpan
                                </button>
                            </form>
                        @else
                            {{ $kurti->catatan_orang_tua ?? '-' }}
                        @endif
                    </td>
                    @if($user->type === 'fasil')
                        <td class="border px-4 py-2">
                            <a href="{{ route('kurtis.edit', $kurti->id) }}"
                               class="text-blue-500 hover:underline text-sm">Edit</a>
                            |
                            <form action="{{ route('kurtis.destroy', $kurti->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Yakin ingin menghapus aktivitas {{ $kurti->aktivitas }} untuk {{ $murid->name }}?')"
                                        class="text-red-600 hover:underline text-sm">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-6">
        <a href="{{ route('dashboard') }}"
           class="inline-block px-4 py-2 bg-gray-200 hover:bg-gray-300 text-sm rounded-lg">
            ← Kembali
        </a>
    </div>
</div>
@endsection
