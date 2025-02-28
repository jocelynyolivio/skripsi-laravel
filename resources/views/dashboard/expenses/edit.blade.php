@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-6">
    <h1>Edit Expense</h1>

    <form action="{{ route('dashboard.expenses.update', $expense->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" class="form-control" value="{{ old('date', $expense->date) }}" required>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <input type="text" class="form-control" value="{{ $expense->category->name }}" readonly>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="text" class="form-control" value="Rp. {{ number_format($expense->amount, 2, ',', '.') }}" readonly>
        </div>

        <div class="mb-3">
            <label for="supplier_id" class="form-label">Supplier</label>
            <select name="supplier_id" class="form-control">
                <option value="">-- Select Supplier --</option>
                @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" {{ $expense->supplier_id == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->nama }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="dental_material_id" class="form-label">Dental Material</label>
            <select name="dental_material_id" class="form-control">
                <option value="">-- Select Material --</option>
                @foreach ($dentalMaterials as $material)
                <option value="{{ $material->id }}" {{ $expense->dental_material_id == $material->id ? 'selected' : '' }}>
                    {{ $material->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="text" class="form-control" value="{{ $expense->quantity }}" readonly>
        </div>

        <div class="mb-3">
            <label for="expired_at" class="form-label">Expiration Date</label>
            <input type="date" name="expired_at" class="form-control" value="{{ old('expired_at', $expense->expired_at) }}">
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control">{{ old('description', $expense->description) }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Update Expense</button>
    </form>
</div>
@endsection
