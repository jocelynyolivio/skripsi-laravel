<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Struk Transaksi - {{ $transaction->transaction_code ?? 'N/A' }}</title>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                margin: 0;
            }
        }

        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 15px auto;
            max-width: 650px;
            color: #333;
            background-color: #fff;
            line-height: 1.6;
        }

        .kop {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }

        .kop h1 {
            margin: 0 0 5px 0;
            font-size: 20px;
            font-weight: 600;
            color: #000;
        }

        .kop p {
            margin: 1px 0;
            font-size: 12px;
            color: #555;
        }

        .detail {
            margin-bottom: 20px;
            font-size: 13px;
        }

        .detail table {
            width: 100%;
            border-collapse: collapse;
        }

        .detail td {
            padding: 4px 0;
            vertical-align: top;
        }

        .detail td strong {
            width: 100px;
            display: inline-block;
            font-weight: 600;
            color: #000;
        }
        .detail td:nth-child(odd) {
            color: #000;
        }

        .table-items {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .table-items thead {
            background-color: #f0f0f0;
            color: #000;
            border-bottom: 2px solid #ddd;
        }

        .table-items th,
        .table-items td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            color: #333;
        }
        .table-items th {
            font-weight: 600;
            font-size: 13px;
        }
        .table-items td:nth-child(1), /* No */
        .table-items td:nth-child(2), /* Item # */
        .table-items td:nth-child(4), /* Qty */
        .table-items td:nth-child(6)  /* Disc % */
        {
            text-align: center;
        }
        .table-items td:nth-child(5), /* Harga Satuan */
        .table-items td:nth-child(7), /* Disc Rp */
        .table-items td:nth-child(8)  /* Sub Total */
        {
            text-align: right;
        }

        .footer {
            font-size: 14px;
            color: #333;
            border-top: 1px solid #ccc;
            padding-top: 15px;
            margin-top: 25px;
        }

        .footer .total-section {
            text-align: right;
            margin-bottom: 15px;
        }
        .footer .total-section p {
             margin: 5px 0;
        }

        .footer .grand-total-calculated { /* Menggunakan kelas baru untuk grand total hasil kalkulasi */
            font-weight: 700;
            font-size: 18px;
            color: #000;
        }

        .payment-info {
            margin-top:15px;
        }
        .payment-info h5 {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #000;
        }

        .payment-info h6 {
            font-size: 13px;
            font-weight: 500;
            margin: 4px 0;
            color: #333;
        }
         .payment-info h6 strong {
            font-weight: 600;
         }

        .payment-info p {
            font-size: 14px;
            margin: 6px 0;
            color: #333;
        }

        .payment-info p strong {
            font-weight: 600;
            color: #000;
        }

        .buttons-container {
            text-align: center;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px dashed #ccc;
        }

        button.no-print {
            display: inline-block;
            background-color: #5cb85c;
            border: none;
            color: white;
            padding: 10px 18px;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
            transition: background-color 0.3s ease;
        }
        button.no-print#backBtn {
            background-color: #777;
        }

        button.no-print:hover {
            opacity: 0.85;
        }
    </style>
</head>

<body>
    <div class="kop">
        <h1>SenyumQu Dental Clinic</h1>
        <p>Jl. Jaksa Agung Suprapto No. 44-46 Kav. B, Kel. Rampal Celaket, Kec. Klojen, Malang 65111</p>
        <p>HP/WA: 0821 2720 2497 | No. Izin: 1599.01</p>
    </div>

    <div class="detail">
        <table>
            <tr>
                <td><strong>ID Pasien:</strong></td>
                <td>{{ optional($transaction->medicalRecord?->patient)->id_member ?? optional($transaction->patient)->id_member ?? 'N/A' }}</td>
                <td><strong>Nama:</strong></td>
                <td>
                    @php
                        $patient = $transaction->medicalRecord?->patient ?? $transaction->patient;
                        $patientName = trim(optional($patient)->fname . ' ' . optional($patient)->mname . ' ' . optional($patient)->lname);
                    @endphp
                    {{ $patientName ?: 'N/A' }}
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
                <td>{{ $transaction->created_at->format('d M Y, H:i') }}</td>
                <td><strong>No. Struk:</strong></td>
                <td>{{ $transaction->transaction_code ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

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
                <th>Sub Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $subtotalBeforeOverallDiscount = 0; // Inisialisasi subtotal sebelum diskon keseluruhan
            @endphp
            @forelse($transaction->items as $index => $item)
            @php
                $unitPrice = $item->unit_price ?? 0;
                $quantity = $item->quantity ?? 1;
                $itemDiscountAmount = $item->discount ?? 0; // Diskon per item
                
                // Harga setelah diskon per item (jika ada), sebelum dikali kuantitas
                // Biasanya final_price di item sudah (unit_price - item_discount) * quantity, atau unit_price_after_item_discount * quantity
                // Untuk amannya, kita hitung dari unit_price dan item_discount
                $priceAfterItemDiscount = $unitPrice - $itemDiscountAmount;
                $subtotalForItem = $priceAfterItemDiscount * $quantity; // Ini adalah final_price untuk item ini

                $discountPercentage = ($unitPrice > 0 && $quantity > 0) ? (($itemDiscountAmount * $quantity) / ($unitPrice * $quantity)) * 100 : 0; // Persentase diskon dari total harga item sebelum diskon
                // Atau jika $item->discount adalah total diskon untuk item (bukan per unit):
                // $discountPercentage = ($unitPrice * $quantity > 0) ? ($itemDiscountAmount / ($unitPrice * $quantity)) * 100 : 0;
                
                $subtotalBeforeOverallDiscount += $subtotalForItem;
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ optional($item->procedure)->item_code ?? optional($item->procedure)->id ?? '-' }}</td>
                <td>{{ optional($item->procedure)->name ?? $item->custom_item_name ?? 'N/A' }}</td>
                <td>{{ $quantity }}</td>
                <td>{{ number_format($unitPrice, 0, ',', '.') }}</td>
                <td>{{ number_format($discountPercentage, 0) }}%</td>
                <td>{{ number_format($itemDiscountAmount, 0, ',', '.') }}</td> {{-- Ini adalah diskon per unit, atau total diskon item? Asumsi per unit. Jika total, ok. --}}
                <td>{{ number_format($subtotalForItem, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center;">Tidak ada item tindakan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        @php
            // Kalkulasi Grand Total di view
            $calculatedGrandTotal = $subtotalBeforeOverallDiscount - ($transaction->discount_overall ?? 0) + ($transaction->tax_amount ?? 0);

            // Kalkulasi Total Dibayar di view
            $calculatedTotalPaid = 0;
            if (isset($transaction->payments) && $transaction->payments->count() > 0) {
                foreach ($transaction->payments as $payment) {
                    $calculatedTotalPaid += ($payment->amount ?? 0);
                }
            }

            // Kalkulasi Sisa Tagihan di view
            $calculatedRemainingAmount = $calculatedGrandTotal - $calculatedTotalPaid;
        @endphp

        <div class="total-section">
            <p>Subtotal: Rp {{ number_format($subtotalBeforeOverallDiscount, 0, ',', '.') }}</p>
            @if(($transaction->discount_overall ?? 0) > 0)
            <p>Diskon Transaksi: Rp ({{ number_format($transaction->discount_overall, 0, ',', '.') }})</p>
            @endif
            @if(($transaction->tax_amount ?? 0) > 0)
            <p>Pajak ({{ $transaction->tax_percentage ?? 0 }}%): Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</p>
            @endif
            {{-- Menggunakan hasil kalkulasi view --}}
            <p class="grand-total-calculated">Grand Total: Rp {{ number_format($calculatedGrandTotal, 0, ',', '.') }}</p>
        </div>

        <div class="payment-info">
            <h5><strong>Detail Pembayaran:</strong></h5>
            @if(isset($transaction->payments) && $transaction->payments->count() > 0)
                @foreach($transaction->payments as $payment)
                <h6>
                    <strong>{{ $loop->iteration }}. {{ $payment->payment_method }}</strong>
                    @if($payment->coa) ({{ $payment->coa->name }}) @endif
                    @if($payment->reference_number) <small>[Ref: {{ $payment->reference_number }}]</small> @endif
                    : Rp {{ number_format($payment->amount ?? 0, 0, ',', '.') }}
                </h6>
                @endforeach
            @else
            <p><em>Belum ada data pembayaran.</em></p>
            @endif
            <hr style="border-top: 1px dashed #ccc; margin: 10px 0;">
            {{-- Menggunakan hasil kalkulasi view --}}
            <p><strong>Total Dibayar:</strong> Rp {{ number_format($calculatedTotalPaid, 0, ',', '.') }}</p>
            <p><strong>Sisa Tagihan:</strong> Rp {{ number_format($calculatedRemainingAmount, 0, ',', '.') }}</p>
            <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $transaction->payment_status ?? 'unknown')) }}</p>
        </div>
        <p style="text-align:center; font-size:12px; margin-top: 20px;">Terima kasih atas kunjungan Anda!</p>
    </div>

    <div class="buttons-container no-print">
        <button id="backBtn" onclick="goBack()" style="margin-right: 10px;">
            ← Kembali
        </button>
        <button id="printBtn" onclick="printAndHideButtons()">Cetak Struk</button>
    </div>

</body>
</html>

<script>
    function printAndHideButtons() {
        const printButton = document.getElementById('printBtn');
        const backButton = document.getElementById('backBtn');

        if (printButton) printButton.style.display = 'none';
        if (backButton) backButton.style.display = 'none';

        window.print();

        setTimeout(function() {
            if (printButton) printButton.style.display = 'inline-block';
            if (backButton) backButton.style.display = 'inline-block';
        }, 500);
    }

    function goBack() {
        if (window.history.length > 1) {
            window.history.back();
        } else {
            // Ganti dengan route dashboard Anda yang benar jika berbeda
            window.location.href = "{{ route('dashboard.transactions.index') }}";
        }
    }
</script>