<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi</title>
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }

        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .kop {
            text-align: center;
            margin-bottom: 20px;
        }

        .kop h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .kop p {
            margin: 2px 0;
            font-size: 14px;
        }

        .detail {
            margin-top: 20px;
        }

        .detail td {
            padding: 5px;
            vertical-align: top;
        }

        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 12px;
        }

        .table-items th,
        .table-items td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .table-items th {
            background-color: #f0f0f0;
        }

        .footer {
            margin-top: 20px;
        }

        .footer p {
            margin: 5px 0;
            font-size: 12px;
        }

        .footer .total {
            font-weight: bold;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <!-- Kop Klinik -->
    <div class="kop">
        <h1>SenyumQu Dental Clinic</h1>
        <p>Jl. Jaksa Agung Suprapto No. 44-46 Kav. B</p>
        <p>Kel. Rampal Celaket, Kec. Klojen, Malang 65111</p>
        <p>HP/WA: 0821 2720 2497</p>
        <p>No. 1599.01</p>
    </div>

    <!-- Detail Transaksi -->
    <div class="detail">
        <table>
            <tr>
                <td><strong>ID Pasien:</strong></td>
                <td>{{ $transaction->medicalRecord->reservation->patient->id }}</td>
                <td><strong>Nama:</strong></td>
                <td>{{ $transaction->medicalRecord->reservation->patient->name }}</td>
            </tr>
            <tr>
                <td><strong>Drg:</strong></td>
                <td>{{ $transaction->medicalRecord->reservation->doctor->name }}</td>
                <td><strong>SIP:</strong></td>
                <td>{{ $transaction->medicalRecord->reservation->doctor->sip ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal:</strong></td>
                <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>

    <!-- Tabel Prosedur -->
    <table class="table-items">
        <thead>
            <tr>
                <th>No</th>
                <th>Item #</th>
                <th>Item Name</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Disc %</th>
                <th>Disc Rp</th>
                <th>Sub Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
    @php $total = 0; @endphp
    @foreach($transaction->medicalRecord->procedures as $index => $procedure)
        @php
            $unitPrice = $procedure->pivot->price; // Ambil harga dari pivot table
            $subtotal = $unitPrice; // Karena quantity = 1
            $total += $subtotal;
        @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $procedure->id }}</td>
            <td>{{ $procedure->name }}</td>
            <td>1</td>
            <td>Rp {{ number_format($unitPrice, 0, ',', '.') }}</td>
            <td>0</td> <!-- Diskon % -->
            <td>0</td> <!-- Diskon Rp -->
            <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
        </tr>
    @endforeach
</tbody>


    </table>

    <!-- Footer -->
    <div class="footer">
    <p class="total">Total: Rp {{ number_format($total, 0, ',', '.') }}</p>
    <p>1. Jenis Pembayaran: {{ ucfirst($transaction->payment_type) }}</p>
    <p>2. Media Transaksi: {{ $transaction->payment_media ?? 'Tidak diketahui' }}</p>
</div>



    <button class="no-print" onclick="window.print()">Cetak Struk</button>
</body>

</html>