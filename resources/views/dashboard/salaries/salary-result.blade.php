<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summary Presensi</title>
</head>
<body>
    <h1>Summary Presensi</h1>

    <table border="1">
        <thead>
            <tr>
                <th>Sheet</th>
                <th>Tanggal</th>
                <th>Departemen</th>
                <th>Nama</th>
                <th>No. ID</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataSummary as $data)
                <tr>
                    <td>{{ $data['sheet'] }}</td>
                    <td>{{ $data['tanggal'] }}</td>
                    <td>{{ $data['departemen'] }}</td>
                    <td>{{ $data['nama'] }}</td>
                    <td>{{ $data['no_id'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
