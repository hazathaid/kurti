@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow">
    <h2 class="text-xl font-bold mb-4">Edit Data Kurti</h2>

    <form method="POST" action="{{ route('kurtis.update', $kurti->id) }}">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Bulan</label>
                <input type="month" name="bulan" value="{{ old('bulan', $kurti->bulan) }}"
                       class="w-full border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Pekan</label>
                <input type="text" name="pekan" value="{{ old('pekan', $kurti->pekan) }}"
                       class="w-full border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Aktivitas</label>
                <input type="text" name="aktivitas" value="{{ old('aktivitas', $kurti->aktivitas) }}"
                       class="w-full border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Amanah Rumah</label>
                <input type="text" name="amanah_rumah" value="{{ old('amanah_rumah', $kurti->amanah_rumah) }}"
                       class="w-full border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Capaian</label>
                <input type="text" name="capaian" value="{{ old('capaian', $kurti->capaian) }}"
                       class="w-full border-gray-300 rounded-lg">
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('dashboard') }}"
               class="mr-2 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                Batal
            </a>
            <button type="submit"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Update
            </button>
        </div>
    </form>
</div>
@endsection
