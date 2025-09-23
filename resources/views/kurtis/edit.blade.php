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
                <input type="month" name="bulan" value="{{ old('bulan', $kurti->group->bulan) }}"
                       class="w-full border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Pekan</label>
                <select name="pekan" class="w-full border-gray-300 rounded-lg">
                    <option value="1" {{ old('pekan', $kurti->group->pekan) == 1 ? 'selected' : '' }}>Pekan 1</option>
                    <option value="2" {{ old('pekan', $kurti->group->pekan) == 2 ? 'selected' : '' }}>Pekan 2</option>
                    <option value="3" {{ old('pekan', $kurti->group->pekan) == 3 ? 'selected' : '' }}>Pekan 3</option>
                    <option value="4" {{ old('pekan', $kurti->group->pekan) == 4 ? 'selected' : '' }}>Pekan 4</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Aktivitas</label>
                <textarea name="aktivitas" class="w-full border-gray-300 rounded-lg">{{ old('aktivitas', $kurti->aktivitas) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Amanah Rumah</label>
                <textarea name="amanah_rumah" class="w-full border-gray-300 rounded-lg">{{ old('amanah_rumah', $kurti->amanah_rumah) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Capaian</label>
                <textarea name="capaian" class="w-full border-gray-300 rounded-lg">{{ old('capaian', $kurti->capaian) }}</textarea>               <!-- Changed from input to textarea -->
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
