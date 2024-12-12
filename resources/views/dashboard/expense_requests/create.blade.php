@extends('dashboard.layouts.main')

@section('container')
<div class="container">
    <h1>Create Expense Request</h1>

    <!-- Menampilkan Error Validasi -->
    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Form Request Expense -->
    <form action="{{ route('dashboard.expense_requests.store') }}" method="POST">
        @csrf

        <!-- Nama Barang -->
        <div class="mb-3">
            <label for="item_name" class="form-label">Item Name</label>
            <input type="text" name="item_name" id="item_name" class="form-control" value="{{ old('item_name') }}" required>
        </div>

        <!-- Deskripsi -->
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
        </div>

        <!-- Jumlah Barang -->
        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" value="{{ old('quantity') }}" required>
        </div>

        <!-- Perkiraan Biaya -->
        <div class="mb-3">
            <label for="estimated_cost" class="form-label">Estimated Cost</label>
            <input type="number" step="0.01" name="estimated_cost" id="estimated_cost" class="form-control" value="{{ old('estimated_cost') }}" required>
        </div>

        <!-- Tombol Submit -->
        <button type="submit" class="btn btn-success">Submit Request</button>
    </form>
</div>
@endsection
