@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-6">
    <h1>Add Expense</h1>

    <!-- Form Tambah Expense -->
    <form action="{{ route('dashboard.expenses.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="expense_date" class="form-label">Date</label>
            <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date') }}" required>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control amount" value="{{ old('amount') }}" required>
        </div>

        <div class="mb-3">
            <label for="coa_out" class="form-label">Bayar Dari (Akun Kas/Bank)</label>
            <select class="form-control" id="coa_out" name="coa_out" required>
                <option value="">-- Pilih Akun Kas/Bank --</option>
                @foreach ($coa as $account)
                <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="coa_in" class="form-label">Dibebankan Ke (Akun Beban)</label>
            <select class="form-control" id="coa_in" name="coa_in" required>
                <option value="">-- Pilih Akun Beban --</option>
                @foreach ($coa as $account)
                <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Save</button>
    </form>
</div>
@endsection