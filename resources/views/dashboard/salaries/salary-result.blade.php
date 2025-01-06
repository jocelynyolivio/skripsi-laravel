<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Upload File</title>
</head>
<body>
    <h1>Data Gaji</h1>
    <table border="1">
        <thead>
            <tr>
                @if (!empty($sheet))
                    @foreach ($sheet[1] as $header) <!-- Baris pertama sebagai header -->
                        <th>{{ $header }}</th>
                    @endforeach
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($sheet as $key => $row)
                @if ($key > 1) <!-- Lewati baris header -->
                    <tr>
                        @foreach ($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>
