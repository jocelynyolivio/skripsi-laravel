@extends('dashboard.layouts.main')

@section('container')
<div class="container">
    <h1>Add Expense</h1>

    <!-- Form Filter Kategori -->
    <form action="{{ route('dashboard.expenses.create') }}" method="GET">
        <div class="mb-3">
            <label for="category_id" class="form-label">Category</label>
            <select name="category_id" id="category_id" class="form-control" required>
                <option value="">-- Select Category --</option>
                @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <!-- Form Tambah Expense -->
    @if(request('category_id'))
    <form action="{{ route('dashboard.expenses.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" class="form-control" value="{{ old('date') }}" required>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount') }}" required>
        </div>

        <input type="hidden" name="category_id" value="{{ request('category_id') }}">

        <!-- Jika kategori adalah Bahan Baku -->
        @if(isset($dentalMaterials) && $categories->find(request('category_id'))->name === 'Bahan Baku')
        <div class="mb-3">
            <label for="dental_material_id" class="form-label">Dental Material</label>
            <select name="dental_material_id" class="form-control" required>
                <option value="">-- Select Material --</option>
                @foreach ($dentalMaterials as $material)
                <option value="{{ $material->id }}" {{ old('dental_material_id') == $material->id ? 'selected' : '' }}>
                    {{ $material->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" name="quantity" class="form-control" value="{{ old('quantity') }}" required>
        </div>

        <!-- Input untuk Tanggal Kadaluarsa -->
        <div class="mb-3">
            <label for="expired_at" class="form-label">Expiration Date</label>
            <input type="date" name="expired_at" class="form-control" value="{{ old('expired_at') }}">
        </div>
        @endif

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Save</button>
    </form>
    @endif
</div>
@endsection
