<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Weekly Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2, h3 { text-align: center; margin: 0; padding: 0; }
        .info { margin-top: 20px; margin-bottom: 10px; }
        .info td { padding: 3px 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; vertical-align: top; }
        th { background-color: #f2f2f2; text-align: center; }
    </style>
</head>
<body>
    <h2>WEEKLY REPORT</h2>
    <h3>{{ $classroom->name }} SAI SUKABUMI</h3>

    <table class="info">
        <tr>
            <td><strong>Nama</strong> : {{ $murid->name }}</td>
            <td><strong>Pekan</strong> : {{ $group->pekan }}</td>
        </tr>
        <tr>
            <td><strong>Kelas</strong> : {{ $classroom->name }}</td>
            <td><strong>Bulan</strong> : {{ $group->bulan }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">Aktivitas</th>
                <th style="width: 25%;">Capaian</th>
                <th style="width: 25%;">Amanah di Rumah</th>
                <th style="width: 25%;">Catatan Ayah/Bunda</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan as $index => $item)
            <tr>
                <td style="text-align:center;">{{ $index + 1 }}</td>
                <td>{{ $item->aktivitas }}</td>
                <td>{{ $item->capaian }}</td>
                <td>{{ $item->_rumah }}</td>
                <td>{{ $item->catatan_orang_tua }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
