@extends('layouts.app')
@section('content')
    <div class="max-w-10xl mx-auto p-6 bg-white rounded-lg shadow">
        <h2 class="text-xl font-bold mb-4">Tambah Data Kurti - {{ $murid->name }}</h2>

        <form method="POST" action="{{ route('kurtis.store') }}">
            @csrf
            <input type="hidden" name="murid_id" value="{{ $murid->id }}">
            <input type="hidden" name="classroom_id" value="{{ $murid->first_classroom->id }}">

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300 rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2 text-left">Bulan</th>
                            <th class="border px-3 py-2 text-left">Pekan</th>
                            <th class="border px-3 py-2 text-left">Aktivitas</th>
                            <th class="border px-3 py-2 text-left">Amanah Rumah</th>
                            <th class="border px-3 py-2 text-left">Capaian</th>
                            <th class="border px-3 py-2 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="rows-container">
                        <tr>
                            <td class="border px-3 py-2">
                                <input type="month" name="kurtis[0][bulan]" class="w-full border-gray-300 rounded-lg">
                            </td>
                            <td class="border px-3 py-2">
                                <select name="kurtis[0][pekan]" class="w-full border-gray-300 rounded-lg">
                                    <option value="1">Pekan 1</option>
                                    <option value="2">Pekan 2</option>
                                    <option value="3">Pekan 3</option>
                                    <option value="4">Pekan 4</option>
                                </select>
                            </td>
                            <td class="border px-3 py-2">
                                <input type="text" name="kurtis[0][aktivitas]" class="w-full border-gray-300 rounded-lg">
                            </td>
                            <td class="border px-3 py-2">
                                <input type="text" name="kurtis[0][amanah_rumah]" class="w-full border-gray-300 rounded-lg">
                            </td>
                            <td class="border px-3 py-2">
                                <input type="text" name="kurtis[0][capaian]" class="w-full border-gray-300 rounded-lg">
                            </td>
                            <td class="border px-3 py-2 text-center">
                                <button type="button" onclick="removeRow(this)" class="text-red-500 hover:text-red-700">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <button type="button" onclick="addRow()"
                        class="px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    + Tambah Baris
                </button>
            </div>

            <div class="mt-6">
                <a href="{{ route('dashboard') }}"
                class="mr-2 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    Batal
                </a>

                <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>

    <script>
        let rowIndex = 1;

        function addRow() {
            const container = document.getElementById('rows-container');
            const newRow = `
                <tr>
                    <td class="border px-3 py-2">
                        <input type="month" name="kurtis[${rowIndex}][bulan]" class="w-full border-gray-300 rounded-lg">
                    </td>
                    <td class="border px-3 py-2">
                        <select name="kurtis[${rowIndex}][pekan]" class="w-full border-gray-300 rounded-lg">
                            <option value="1">Pekan 1</option>
                            <option value="2">Pekan 2</option>
                            <option value="3">Pekan 3</option>
                            <option value="4">Pekan 4</option>
                        </select>
                    </td>
                    <td class="border px-3 py-2">
                        <input type="text" name="kurtis[${rowIndex}][aktivitas]" class="w-full border-gray-300 rounded-lg">
                    </td>
                    <td class="border px-3 py-2">
                        <input type="text" name="kurtis[${rowIndex}][amanah_rumah]" class="w-full border-gray-300 rounded-lg">
                    </td>
                    <td class="border px-3 py-2">
                        <input type="text" name="kurtis[${rowIndex}][capaian]" class="w-full border-gray-300 rounded-lg">
                    </td>
                    <td class="border px-3 py-2 text-center">
                        <button type="button" onclick="removeRow(this)" class="text-red-500 hover:text-red-700">Hapus</button>
                    </td>
                </tr>
            `;
            container.insertAdjacentHTML('beforeend', newRow);
            rowIndex++;
        }

        function removeRow(button) {
            button.closest('tr').remove();
        }
    </script>
@endsection
