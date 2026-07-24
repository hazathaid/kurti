@extends('layouts.app')
@section('content')
    <div class="page-shell">
        <a href="{{ route('dashboard') }}" class="mb-4 inline-flex text-sm font-semibold text-slate-600 hover:text-emerald-700">← Kembali</a>
        <div class="page-header">
            <div>
                <p class="page-eyebrow">Aktivitas Baru</p>
                <h1 class="page-title">Tambah Kurti — {{ $murid->name }}</h1>
                <p class="page-description">Tambahkan satu atau beberapa aktivitas dalam satu kali penyimpanan.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('kurtis.store') }}" class="surface p-4 sm:p-6">
            @csrf
            <input type="hidden" name="murid_id" value="{{ $murid->id }}">
            <input type="hidden" name="classroom_id" value="{{ $murid->first_classroom->id }}">

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th>Pekan</th>
                            <th>Aktivitas</th>
                            <th>Amanah Rumah</th>
                            <th>Capaian</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="rows-container">
                        <tr>
                            <td class="min-w-44">
                                <input type="month" name="kurtis[0][bulan]" class="field-control">
                            </td>
                            <td class="min-w-36">
                                <select name="kurtis[0][pekan]" class="field-control">
                                    <option value="1">Pekan 1</option>
                                    <option value="2">Pekan 2</option>
                                    <option value="3">Pekan 3</option>
                                    <option value="4">Pekan 4</option>
                                </select>
                            </td>
                            <td class="min-w-64">
                                <textarea name="kurtis[0][aktivitas]" class="field-control min-h-24 resize-none" placeholder="Aktivitas murid" oninput="this.style.height='auto'; this.style.height=this.scrollHeight+'px'"></textarea>
                            </td>
                            <td class="min-w-64">
                                <textarea name="kurtis[0][amanah_rumah]" class="field-control min-h-24 resize-none" placeholder="Tindak lanjut di rumah" oninput="this.style.height='auto'; this.style.height=this.scrollHeight+'px'"></textarea>
                            </td>
                            <td class="min-w-64">
                                <textarea name="kurtis[0][capaian]" class="field-control min-h-24 resize-none" placeholder="Capaian murid" oninput="this.style.height='auto'; this.style.height=this.scrollHeight+'px'"></textarea>
                            </td>
                            <td class="text-center">
                                <button type="button" onclick="removeRow(this)" class="text-sm font-semibold text-rose-600 hover:underline">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <button type="button" onclick="addRow()"
                        class="btn-secondary">
                    + Tambah Baris
                </button>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                <a href="{{ route('dashboard') }}"
                class="btn-secondary">
                    Batal
                </a>

                <button type="submit"
                        class="btn-primary">
                    Simpan Aktivitas
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
                    <td class="min-w-44">
                        <input type="month" name="kurtis[${rowIndex}][bulan]" class="field-control">
                    </td>
                    <td class="min-w-36">
                        <select name="kurtis[${rowIndex}][pekan]" class="field-control">
                            <option value="1">Pekan 1</option>
                            <option value="2">Pekan 2</option>
                            <option value="3">Pekan 3</option>
                            <option value="4">Pekan 4</option>
                        </select>
                    </td>
                    <td class="min-w-64">
                        <textarea name="kurtis[${rowIndex}][aktivitas]" class="field-control min-h-24" placeholder="Aktivitas murid"></textarea>
                    </td>
                    <td class="min-w-64">
                        <textarea name="kurtis[${rowIndex}][amanah_rumah]" class="field-control min-h-24" placeholder="Tindak lanjut di rumah"></textarea>
                    </td>
                    <td class="min-w-64">
                        <textarea name="kurtis[${rowIndex}][capaian]" class="field-control min-h-24" placeholder="Capaian murid"></textarea>
                    </td>
                    <td class="text-center">
                        <button type="button" onclick="removeRow(this)" class="text-sm font-semibold text-rose-600 hover:underline">Hapus</button>
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
