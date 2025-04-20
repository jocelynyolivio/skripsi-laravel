@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-6">
    <h1>Create Expense</h1>
    @if(isset($expense))
    <div class="alert alert-info">
        You are duplicating expense #{{ $expense->id }}. Feel free to adjust the values below.
    </div>
    @endif

    <!-- Form Tambah Expense -->
    <form action="{{ route('dashboard.expenses.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="expense_date" class="form-label">Date</label>
            <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', isset($expense) ? now()->toDateString() : '') }}" required>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control amount" value="{{ old('amount', $expense->amount ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label for="coa_out" class="form-label">Pay From (Cash/Bank Account)</label>
            <select name="coa_out" class="form-control" required>
                @foreach($coa as $account)
                <option value="{{ $account->id }}"
                    {{ old('coa_out', $expense->coa_out ?? '') == $account->id ? 'selected' : '' }}>
                    {{ $account->code }} - {{ $account->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="coa_in" class="form-label">Charged To (Expense Account)
            </label>
            <select name="coa_in" class="form-control" required>
                @foreach($coa as $account)
                <option value="{{ $account->id }}"
                    {{ old('coa_in', $expense->coa_in ?? '') == $account->id ? 'selected' : '' }}>
                    {{ $account->code }} - {{ $account->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control">{{ old('description', $expense->description ?? '') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Save</button>
    </form>
</div>
@endsection