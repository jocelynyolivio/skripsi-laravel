@extends('dashboard.layouts.main')

@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Expenses', 'url' => route('dashboard.expenses.index')],
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
                        <h5 class="mb-0">Expense Details #{{ $expense->id }}</h5>
                        <div>
                            <a href="{{ route('dashboard.expenses.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Expense Date:</strong> {{ $expense->expense_date}}
                            </div>
                            <div class="info-item">
                                <strong>Created By:</strong> {{ $expense->admin?->name ?? 'N/A' }}
                            </div>
                            <div class="info-item">
                                <strong>COA Out:</strong> {{ $expense->coaOut->name ?? '-' }}
                            </div>
                            <div class="info-item">
                                <strong>Payment Method:</strong> {{ $expense->payment_method ?? '-' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>COA In:</strong> {{ $expense->coaIn->name ?? '-' }}
                            </div>
                            <div class="info-item">
                                <strong>Amount:</strong> Rp {{ number_format($expense->amount, 2, ',', '.') }}
                            </div>
                            <div class="info-item">
                                <strong>Reference Number:</strong> {{ $expense->reference_number ?? '-' }}
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-12">
                            <div class="info-item">
                                <strong>Description:</strong>
                                <div class="border rounded p-3 mt-2 bg-light">
                                    {{ $expense->description ?? 'No description provided' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted text-xs">
                            <strong>Created:</strong> {{ $expense->created_at->format('d M Y H:i') }} | 
                            <strong>Updated:</strong> {{ $expense->updated_at->format('d M Y H:i') }}
                        </div>
                        <div class="btn-group">
                            <!-- <a href="{{ route('dashboard.expenses.edit', $expense->id) }}" class="btn btn-warning btn-sm me-2">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a> -->
                            <a href="{{ route('dashboard.expenses.duplicate', $expense->id) }}" class="btn btn-secondary btn-sm me-2">
                                <i class="fas fa-copy me-1"></i> Duplicate
                            </a>
                            <!-- <form action="{{ route('dashboard.expenses.destroy', $expense->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this expense?')">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </form> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .info-item {
        margin-bottom: 0.8rem;
    }
    .card-header {
        padding: 1rem 1.5rem;
    }
    .card-body {
        padding: 1.5rem;
    }
    .card-footer {
        padding: 1rem 1.5rem;
        background-color: rgba(0, 0, 0, 0.02);
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }
</style>
@endsection