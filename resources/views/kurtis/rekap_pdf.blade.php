<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Kurti {{$classroom->name}}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h3>Rekap Kurti {{$classroom->name}}</h3>

    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Bulan</th>
                <th>Pekan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groupedByMurid as $murid)
                @foreach($murid->groups as $bulanGroup)
                    @foreach($bulanGroup->pekans as $pekanGroup)
                        <tr>
                            <td>{{ $murid->murid_name }}</td>
                            <td>{{ $bulanGroup->bulan }}</td>
                            <td>Pekan {{ $pekanGroup->pekan }}</td>
                            <td>{{ $pekanGroup->status }}</td>
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
