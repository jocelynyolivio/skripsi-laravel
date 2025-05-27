@extends('dashboard.layouts.main')

@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Purchase Invoices', 'url' => route('dashboard.purchases.index')],
            ['text' => 'Detail Invoice']
        ]
    ])
@endsection

@section('container')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Detail Invoice #{{ $purchaseInvoice->invoice_number }}</h5>
                        <h5 class="mb-0">{{ \Carbon\Carbon::parse($purchaseInvoice->invoice_date)->format('d M Y') }}</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-item"><strong>Supplier:</strong> {{ optional($purchaseInvoice->supplier)->name ?? 'N/A' }}</div>
                            <div class="info-item"><strong>No. Purchase Order:</strong> {{ optional($purchaseInvoice->purchaseOrder)->po_number ?? 'N/A' }}</div>
                            <div class="info-item"><strong>Jatuh Tempo:</strong> {{ $purchaseInvoice->due_date ? \Carbon\Carbon::parse($purchaseInvoice->due_date)->format('d M Y') : '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item"><strong>Total Tagihan:</strong> Rp {{ number_format($purchaseInvoice->grand_total, 0, ',', '.') }}</div>
                            <div class="info-item"><strong>Total Dibayar:</strong> Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
                            <div class="info-item"><strong>Sisa Tagihan:</strong> Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</div>
                            <div class="info-item"><strong>Status:</strong>
                                <span class="badge bg-{{ $purchaseInvoice->status === 'unpaid' ? 'danger' : ($purchaseInvoice->status === 'partial' ? 'warning' : 'success') }}">
                                    {{ ucfirst($purchaseInvoice->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3">Rincian Item</h6>
                    @if ($purchaseInvoice->details->isEmpty())
                        <div class="alert alert-warning">Tidak ada item dalam invoice ini.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Nama Produk</th>
                                        <th>Qty</th>
                                        <th>Harga Satuan</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchaseInvoice->details as $detail)
                                        <tr>
                                            <td>{{ optional($detail->product)->name ?? '-' }}</td>
                                            <td>{{ $detail->quantity }}</td>
                                            <td>Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($detail->quantity * $detail->unit_price, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <hr class="my-4">

                    <h6 class="mb-3">Riwayat Pembayaran</h6>
                    @if ($purchaseInvoice->payments->isEmpty())
                        <div class="alert alert-info text-white">
                            <strong>Belum ada pembayaran yang dilakukan.</strong>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tgl. Bayar</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Jumlah</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Metode</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Akun Pembayaran</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchaseInvoice->payments as $payment)
                                        <tr>
                                            <td class="ps-4">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                                            <td class="text-end pe-4">Rp {{ number_format($payment->purchase_amount, 0, ',', '.') }}</td>
                                            <td>{{ ucfirst($payment->payment_method ?? '-') }}</td>
                                            <td>{{ optional($payment->coa)->name ?? '-' }}</td>
                                            <td><small>{{ $payment->notes ?? '-' }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
