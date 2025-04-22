@extends('dashboard.layouts.main')

@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Transactions', 'url' => route('dashboard.transactions.index')],
            ['text' => 'Detail']
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
                        <h5 class="mb-0">Transaction Details #{{ $transaction->id }}</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Patient:</strong> {{ $transaction->patient->fname}} {{ $transaction->patient->mname}} {{ $transaction->patient->lname}}
                            </div>
                            <div class="info-item">
                                <strong>Admin:</strong> {{ $transaction->admin->name ?? '-' }}
                            </div>
                            <div class="info-item">
                                <strong>Payment Status:</strong> 
                                <span class="badge bg-{{ $transaction->payment_status == 'unpaid' ? 'danger' : 'success' }}">
                                    {{ ucfirst($transaction->payment_status) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Total Amount:</strong> Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                            </div>
                            <div class="info-item">
                                <strong>Unpaid:</strong> Rp {{ number_format($transaction->remaining_amount, 0, ',', '.') }}
                            </div>
                            <div class="info-item">
                                <strong>Medical Record:</strong> 
                                @if ($transaction->medicalRecord)
          
                                @else
                                    Not associated
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3">Transaction Items</h6>
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Procedure</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Quantity</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Unit Price</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Discount</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Total Price</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Final Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transaction->items as $item)
                                    <tr>
                                        <td class="ps-4">
                                            <p class="text-xs font-weight-bold mb-0">{{ $item->procedure->name ?? '-' }}</p>
                                        </td>
                                        <td class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{ $item->quantity }}</p>
                                        </td>
                                        <td class="text-end pe-4">
                                            <p class="text-xs font-weight-bold mb-0">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                        </td>
                                        <td class="text-end pe-4">
                                            <p class="text-xs font-weight-bold mb-0">Rp {{ number_format($item->discount, 0, ',', '.') }}</p>
                                        </td>
                                        <td class="text-end pe-4">
                                            <p class="text-xs font-weight-bold mb-0">Rp {{ number_format($item->total_price, 0, ',', '.') }}</p>
                                        </td>
                                        <td class="text-end pe-4">
                                            <p class="text-xs font-weight-bold mb-0">Rp {{ number_format($item->final_price, 0, ',', '.') }}</p>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3">Payment History</h6>
                    @if ($transaction->payments->isEmpty())
                        <div class="alert alert-info text-white">
                            No payments yet.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Amount</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Method</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transaction->payments as $payment)
                                        <tr>
                                            <td class="ps-4">
                                                <p class="text-xs font-weight-bold mb-0">{{ $payment->created_at->format('d M Y H:i') }}</p>
                                            </td>
                                            <td class="text-end pe-4">
                                                <p class="text-xs font-weight-bold mb-0">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ ucfirst($payment->payment_method ?? '-') }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $payment->notes ?? '-' }}</p>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <div class="text-muted text-xs">
                        <strong>Created At:</strong> {{ $transaction->created_at->format('d M Y H:i') }} | 
                        <strong>Last Edited By:</strong> {{ $transaction->editor->name ?? '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .info-item {
        margin-bottom: 0.5rem;
    }
    .card-header {
        padding: 1rem 1.5rem;
    }
    .card-body {
        padding: 1.5rem;
    }
    .card-footer {
        padding: 1rem 1.5rem;
        background-color: rgba(255, 255, 255, 0.05);
    }
</style>
@endsection