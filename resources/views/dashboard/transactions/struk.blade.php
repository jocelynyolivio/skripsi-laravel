<!DOCTYPE html>
<html lang="id">

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
                <td>{{ optional($transaction->medicalRecord?->patient)->id ?? optional($transaction->patient)->id }}</td>
                <td><strong>Nama:</strong></td>
                <td>{{ optional($transaction->medicalRecord?->patient)->name ?? optional($transaction->patient)->name }}</td>
            </tr>
            <tr>
                <td><strong>Drg:</strong></td>
                <td>{{ optional($transaction->medicalRecord?->doctor)->name ?? 'N/A' }}</td>
                <td><strong>SIP:</strong></td>
                <td>{{ optional($transaction->medicalRecord?->doctor)->sip ?? '-' }}</td>
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
            @php $grandTotal = 0; @endphp
            @foreach($transaction->items as $index => $item)
                @php
                    $unitPrice = $item->unit_price;
                    $discount = $item->discount;
                    $subtotal = $item->final_price; // Harga setelah diskon
                    $discountPercentage = ($unitPrice > 0) ? ($discount / $unitPrice) * 100 : 0;
                    $grandTotal += $subtotal;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ optional($item->procedure)->id ?? '-' }}</td>
                    <td>{{ optional($item->procedure)->name ?? 'N/A' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp {{ number_format($unitPrice, 0, ',', '.') }}</td>
                    <td>{{ number_format($discountPercentage, 2) }}%</td>
                    <td>Rp {{ number_format($discount, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p class="total">Grand Total: Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
        
        <div class="payment-info">
            <p><strong>Pembayaran:</strong></p>
            @if($transaction->payments->count() > 0)
                @foreach($transaction->payments as $payment)
                <p>
                    {{ $loop->iteration }}. {{ $payment->payment_method }}: 
                    Rp {{ number_format($payment->amount, 0, ',', '.') }} 
                </p>
                @endforeach
            @else
                <p>Belum ada pembayaran</p>
            @endif
            
            <p><strong>Sisa Tagihan:</strong> Rp {{ number_format($transaction->remaining_amount, 0, ',', '.') }}</p>
            <p><strong>Status:</strong> {{ ucfirst($transaction->payment_status) }}</p>
        </div>
    </div>

    <button class="no-print" onclick="window.print()">Cetak Struk</button>
</body>

</html>
