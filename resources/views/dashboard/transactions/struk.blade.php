<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Struk Transaksi</title>
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 20px auto;
            max-width: 650px;
            color: #000;
            background-color: #fff;
        }

        .kop {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .kop h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            color: #000;
        }

        .kop p {
            margin: 2px 0;
            font-size: 14px;
            color: #000;
        }

        .detail {
            margin-top: 10px;
            margin-bottom: 25px;
        }

        .detail table {
            width: 100%;
            border-collapse: collapse;
        }

        .detail td {
            padding: 6px 8px;
            vertical-align: top;
            font-size: 14px;
            color: #000;
        }

        .detail td strong {
            width: 110px;
            display: inline-block;
            font-weight: 600;
        }

        .table-items {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 30px;
        }

        .table-items thead {
            background-color: #ddd;
            color: #000;
        }

        .table-items th,
        .table-items td {
            border: 1px solid #000;
            padding: 10px 8px;
            text-align: center;
            color: #000;
        }

        .table-items th {
            font-weight: 700;
            font-size: 14px;
        }

        .table-items tbody tr:hover {
            background-color: #f2f2f2;
        }

        .footer {
            font-size: 16px;
            color: #000;
            border-top: 2px solid #000;
            padding-top: 20px;
            margin-top: 30px;
        }

        .footer .total {
            font-weight: 700;
            font-size: 22px;
            margin-bottom: 20px;
        }

        .payment-info h5 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #000;
        }

        .payment-info h6 {
            font-size: 16px;
            font-weight: 600;
            margin: 6px 0;
            color: #000;
        }

        .payment-info p {
            font-size: 16px;
            margin: 8px 0;
            color: #000;
        }

        .payment-info strong {
            font-weight: 700;
        }

        button.no-print {
            display: inline-block;
            background-color: #000;
            border: none;
            color: white;
            padding: 10px 18px;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }

        button.no-print:hover {
            background-color: #333;
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
                <td>
                    {{
                        trim(
                        (optional($transaction->medicalRecord?->patient)->fname ?? optional($transaction->patient)->fname) . ' ' .
                        (optional($transaction->medicalRecord?->patient)->mname ?? optional($transaction->patient)->mname) . ' ' .
                        (optional($transaction->medicalRecord?->patient)->lname ?? optional($transaction->patient)->lname)
                        )
                    }}
                </td>

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
                <th>Nama Item</th>
                <th>Qty</th>
                <th>Harga Satuan</th>
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
            $subtotal = $item->final_price;
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
            <h5><strong>Pembayaran:</strong></h5>
            @if($transaction->payments->count() > 0)
            @foreach($transaction->payments as $payment)
            <h6>
                {{ $loop->iteration }}. {{ $payment->payment_method }} - {{ $payment->coa->name }}:
                Rp {{ number_format($payment->amount, 0, ',', '.') }}
            </h6>
            @endforeach
            @else
            <p>Belum ada pembayaran</p>
            @endif

            <p><strong>Sisa Tagihan:</strong> Rp {{ number_format($transaction->remaining_amount, 0, ',', '.') }}</p>
            <p><strong>Status:</strong> {{ ucfirst($transaction->payment_status) }}</p>
        </div>
    </div>
    <button class="no-print" id="backBtn" onclick="window.history.back()" style="margin-right: 10px;">
        ← Kembali
    </button>
    <button class="no-print" id="printBtn" onclick="printAndHideButton()">Cetak Struk</button>
</body>

</html>

<script>
    function printAndHideButton() {
        const btn = document.getElementById('printBtn');
        btn.style.display = 'none'; // sembunyikan tombol
        window.print();
        btn.style.display = 'inline-block'; // kembalikan tombol setelah print (opsional)
    }
</script>