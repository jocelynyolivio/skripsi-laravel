@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="mb-4">Stock Adjustment</h3>

    <form action="{{ route('dashboard.stock_cards.adjust.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="dental_material_id" class="form-label">Dental Material</label>
            <select class="form-control" name="dental_material_id" required>
                @foreach($materials as $material)
                    <option value="{{ $material->id }}">{{ $material->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" class="form-control" required value="{{ now()->format('Y-m-d') }}">
        </div>

        <div class="mb-3">
            <label for="quantity_in" class="form-label">Quantity Adjustment (+/-)</label>
            <input type="number" name="quantity_in" class="form-control" placeholder="Contoh: 10 atau -5" required>
        </div>

        <div class="mb-3">
            <label for="note" class="form-label">Note</label>
            <textarea name="note" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Submit Adjustment</button>
        <a href="{{ route('dashboard.stock_cards.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
