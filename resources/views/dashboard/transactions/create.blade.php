@extends('dashboard.layouts.main')

@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['url' => route('dashboard.transactions.index'), 'text' => 'Transactions'],
['text' => 'Create Transaction']
]
])
@endsection

@section('container')
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">
        <div class="card mt-4 shadow-sm">
            <div class="card-header bg-light">
                <h4 class="card-title mb-0">Create Transaction</h4>
                <p class="text-muted mb-0">For Medical Record ID: {{ $medicalRecord->id }}</p>
            </div>
            <div class="card-body">
                @if(empty($proceduresWithPrices))
                    <div class="alert alert-danger text-center" role="alert">
                        <h4 class="alert-heading"><i class="bi bi-x-circle-fill"></i> Cannot Create Transaction</h4>
                        <p>
                            Tidak ada tindakan/prosedur yang ditemukan pada rekam medis ini.
                        </p>
                        <hr>
                        <p class="mb-0">
                            Harap pastikan dokter telah melengkapi rekam medis sebelum melanjutkan ke pembayaran.
                        </p>
                        <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">Go Back</a>
                    </div>
                @else
                <form action="{{ route('dashboard.transactions.store') }}" method="POST">
                    @csrf
                    {{-- Hidden fields untuk data penting --}}
                    <input type="hidden" name="medical_record_id" value="{{ $medicalRecord->id }}">
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                    <input type="hidden" name="admin_id" value="{{ auth()->id() }}">

                    <div class="border rounded p-3 mb-4 bg-light">
                        <h5 class="mb-3">Medical Record Details</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Patient:</strong></p>
                                <p>{{ $medicalRecord->patient->fname }} {{ $medicalRecord->patient->mname }} {{ $medicalRecord->patient->lname }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-1"><strong>Doctor:</strong></p>
                                <p>{{ $medicalRecord->doctor->name }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-1"><strong>Reservation Date:</strong></p>
                                <p>{{ \Carbon\Carbon::parse($medicalRecord->tanggal_reservasi)->format('d F Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3">Cost Details</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th style="width: 30%;">Procedure</th>
                                    <th style="width: 25%;">Price Option</th>
                                    <th style="width: 25%;">Discount</th>
                                    <th style="width: 20%;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proceduresWithPrices as $index => $item)
                                <tr class="procedure-row">
                                    {{-- Hidden inputs untuk setiap baris --}}
                                    <input type="hidden" name="procedure_ids[]" value="{{ $item['procedure']->id }}">
                                    <input type="hidden" class="quantity-input" value="{{ $item['quantity'] }}">
                                    <input type="hidden" class="discount-hidden" name="discount_final[{{ $item['procedure']->id }}]" value="0">

                                    <td>
                                        <strong>{{ $item['procedure']->name }}</strong>
                                        <small class="d-block text-muted">Quantity: x{{ $item['quantity'] }}</small>
                                    </td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input amount-input" type="radio" name="amount[{{ $item['procedure']->id }}]" id="base-{{$index}}" value="{{ $item['basePrice'] }}" checked>
                                            <label class="form-check-label" for="base-{{$index}}">
                                                Base: Rp {{ number_format($item['basePrice'], 0, ',', '.') }}
                                            </label>
                                        </div>
                                        @if($item['promoPrice'])
                                        <div class="form-check">
                                            <input class="form-check-input amount-input" type="radio" name="amount[{{ $item['procedure']->id }}]" id="promo-{{$index}}" value="{{ $item['promoPrice'] }}">
                                            <label class="form-check-label text-success" for="promo-{{$index}}">
                                                Promo: Rp {{ number_format($item['promoPrice'], 0, ',', '.') }}
                                            </label>
                                        </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" name="discount[{{ $item['procedure']->id }}]" class="form-control discount-input" value="0" min="0">
                                            <select class="form-select discount-type" style="max-width: 65px;">
                                                <option value="rp" selected>Rp</option>
                                                <option value="percent">%</option>
                                            </select>
                                        </div>
                                        <small class="text-muted">Applied: Rp <span class="discount-amount-display">0</span></small>
                                    </td>
                                    <td class="text-end">
                                        <strong class="subtotal-display">Rp 0</strong>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label for="voucher" class="form-label">Apply Voucher</label>
                            <select class="form-select" id="voucher" name="voucher">
                                <option value="">-- No Voucher --</option>
                                @foreach ($vouchers as $voucher)
                                <option value="{{ $voucher->birthday_voucher_code }}">{{ $voucher->birthday_voucher_code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 bg-light">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="mb-0">Total Amount:</h5>
                                    <h5 class="mb-0 text-primary" id="total-amount-display">Rp 0</h5>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between align-items-center text-danger">
                                    <h5 class="mb-0">Remaining:</h5>
                                    <h5 class="mb-0" id="remaining-amount-display">Rp 0</h5>
                                </div>
                                <input type="hidden" id="total_amount" name="total_amount" value="0">
                                <input type="hidden" id="remaining_amount" name="remaining_amount" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="border rounded p-3 mt-4">
                        <h5 class="mb-3">Payment Details</h5>
                        <div id="payments-container">
                            <div class="payment-item">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="payment_method" class="form-label">Payment Method</label>
                                        <select class="form-select" id="payment_method" name="payments[0][method]" required>
                                            {{-- Opsi metode pembayaran Anda di sini --}}
                                            <option value="">-- Select Payment Method --</option>
                                            <optgroup label="Tunai">
                                                <option value="tunai">Tunai</option>
                                            </optgroup>
                                            <!-- QRIS -->
                                            <optgroup label="QRIS">
                                                <option value="QRIS BCA">QRIS BCA</option>
                                                <option value="QRIS CIMB Niaga">QRIS CIMB Niaga</option>
                                                <option value="QRIS Mandiri">QRIS Mandiri</option>
                                                <option value="QRIS BRI">QRIS BRI</option>
                                                <option value="QRIS BNI">QRIS BNI</option>
                                                <option value="QRIS Permata">QRIS Permata</option>
                                                <option value="QRIS Maybank">QRIS Maybank</option>
                                                <option value="QRIS Danamon">QRIS Danamon</option>
                                                <option value="QRIS Bank Mega">QRIS Bank Mega</option>
                                            </optgroup>

                                            <!-- Kartu Kredit/Debit -->
                                            <optgroup label="Kartu Kredit/Debit">
                                                <option value="Visa">Visa</option>
                                                <option value="Mastercard">Mastercard</option>
                                                <option value="JCB">JCB</option>
                                                <option value="American Express">American Express (AMEX)</option>
                                                <option value="GPN">GPN (Gerbang Pembayaran Nasional)</option>
                                                <option value="Kartu Kredit BCA">Kartu Kredit BCA</option>
                                                <option value="Kartu Kredit Mandiri">Kartu Kredit Mandiri</option>
                                                <option value="Kartu Kredit BRI">Kartu Kredit BRI</option>
                                                <option value="Kartu Kredit BNI">Kartu Kredit BNI</option>
                                                <option value="Kartu Kredit CIMB Niaga">Kartu Kredit CIMB Niaga</option>
                                            </optgroup>

                                            <!-- Transfer Bank -->
                                            <optgroup label="Transfer Bank">
                                                <option value="Transfer Bank BCA">Transfer Bank BCA</option>
                                                <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                                                <option value="Transfer Bank BRI">Transfer Bank BRI</option>
                                                <option value="Transfer Bank BNI">Transfer Bank BNI</option>
                                                <option value="Transfer Bank CIMB Niaga">Transfer Bank CIMB Niaga</option>
                                                <option value="Transfer Bank Permata">Transfer Bank Permata</option>
                                                <option value="Transfer Bank Maybank">Transfer Bank Maybank</option>
                                            </optgroup>

                                            <!-- E-Wallet -->
                                            <optgroup label="E-Wallet">
                                                <option value="GoPay">GoPay</option>
                                                <option value="OVO">OVO</option>
                                                <option value="Dana">Dana</option>
                                                <option value="LinkAja">LinkAja</option>
                                                <option value="ShopeePay">ShopeePay</option>
                                                <option value="Doku Wallet">Doku Wallet</option>
                                                <option value="PayPal">PayPal</option>
                                            </optgroup>
                                        </select>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="payment_amount" class="form-label">Payment Amount</label>
                                        <input type="number" class="form-control" id="payment_amount" name="payments[0][amount]" min="0" value="0" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="coa_id" class="form-label">Deposit To (Cash/Bank Account)</label>
                                        <select class="form-select" id="coa_id" name="payments[0][coa_id]" required>
                                            <option value="">-- Select Account --</option>
                                            @foreach ($cashAccounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="payment_notes" class="form-label">Notes (Optional)</label>
                                        <input type="text" class="form-control" id="payment_notes" name="payments[0][notes]">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> Create Transaction
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalAmountField = document.getElementById('total_amount');
        const totalAmountDisplay = document.getElementById('total-amount-display');
        const paymentInput = document.getElementById('payment_amount'); // ID diubah
        const remainingAmountField = document.getElementById('remaining_amount');
        const remainingAmountDisplay = document.getElementById('remaining-amount-display');

        function formatCurrency(value) {
            return 'Rp ' + parseFloat(value).toLocaleString('id-ID');
        }

        function calculateTotal() {
            let grandTotal = 0;
            const procedureRows = document.querySelectorAll('.procedure-row');

            procedureRows.forEach(row => {
                const quantity = parseInt(row.querySelector('.quantity-input').value) || 1;
                const selectedPriceInput = row.querySelector('.amount-input:checked');
                const discountInput = row.querySelector('.discount-input');
                const discountType = row.querySelector('.discount-type').value;
                const discountDisplay = row.querySelector('.discount-amount-display');
                const discountHidden = row.querySelector('.discount-hidden');
                const subtotalDisplay = row.querySelector('.subtotal-display');

                if (selectedPriceInput) {
                    let unitPrice = parseFloat(selectedPriceInput.value);
                    let discountValue = parseFloat(discountInput.value) || 0;
                    let baseSubtotal = unitPrice * quantity;

                    let finalDiscount = discountType === 'percent' ? (baseSubtotal * (discountValue / 100)) : discountValue;

                    finalDiscount = Math.min(finalDiscount, baseSubtotal); // Diskon tidak boleh melebihi subtotal

                    discountDisplay.textContent = finalDiscount.toLocaleString('id-ID');
                    discountHidden.value = finalDiscount;

                    let finalSubtotal = Math.max(0, baseSubtotal - finalDiscount);
                    subtotalDisplay.textContent = formatCurrency(finalSubtotal);

                    grandTotal += finalSubtotal;
                }
            });

            totalAmountField.value = grandTotal;
            totalAmountDisplay.textContent = formatCurrency(grandTotal);
            calculateRemainingPayment();
        }

        function calculateRemainingPayment() {
            let total = parseFloat(totalAmountField.value) || 0;
            let payment = parseFloat(paymentInput.value) || 0;

            // Otomatis batasi pembayaran agar tidak melebihi total
            if (payment > total) {
                payment = total;
                paymentInput.value = total;
            }

            let remaining = Math.max(0, total - payment);
            remainingAmountField.value = remaining;
            remainingAmountDisplay.textContent = formatCurrency(remaining);
        }

        // Pasang event listener ke semua elemen yang relevan
        document.querySelectorAll('.amount-input, .discount-input, .discount-type').forEach(el => {
            el.addEventListener('change', calculateTotal);
        });
        document.querySelectorAll('.discount-input').forEach(el => {
            el.addEventListener('input', calculateTotal);
        });

        paymentInput.addEventListener('input', calculateRemainingPayment);

        // Lakukan kalkulasi awal saat halaman dimuat
        calculateTotal();
    });
</script>
@endsection