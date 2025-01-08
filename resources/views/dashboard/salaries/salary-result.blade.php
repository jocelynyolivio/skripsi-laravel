<!-- hapus komen kalo eerorrrr -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summary Presensi</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Summary Presensi</h1>

    <table>
        <thead>
            <tr>
                <th>Sheet</th>
                <th>Departemen</th>
                <th>Nama</th>
                <th>No. ID</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataSummary as $data)
                @foreach ($data['kehadiran'] as $kehadiran)
                    <tr>
                        <td>{{ $data['sheet'] }}</td>
                        <td>{{ $data['departemen'] }}</td>
                        <td>{{ $data['nama'] }}</td>
                        <td>{{ $data['no_id'] }}</td>
                        <td>{{ $kehadiran['tanggal'] }}</td>
                        <td>{{ $kehadiran['jam_masuk'] ?? 'N/A' }}</td>
                        <td>{{ $kehadiran['jam_pulang'] ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>

