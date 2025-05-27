@extends('dashboard.layouts.main')
@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['text' => 'Pricelists', 'url' => route('dashboard.pricelists.index')],
['text' => 'Edit Pricelist for '. $pricelist->procedure->name ]
]
])
@endsection
@section('container')
<div class="container mt-5 col-md-6">
    <h1>Edit Pricelist</h1>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('dashboard.pricelists.update', $pricelist) }}" id="editPricelistForm" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Procedure</label>
            <select name="procedure_id" class="form-control" required>
                <option value="">-- Select Procedure --</option>
                @foreach($procedures as $proc)
                <option value="{{ $proc->id }}" {{ (old('procedure_id') ?? $pricelist->procedure_id) == $proc->id ? 'selected' : '' }}>{{ $proc->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Price</label>
            <input type="number" name="price" step="0.01" class="form-control" value="{{ old('price') ?? $pricelist->price }}" required>
        </div>

        <div class="mb-3 form-check">

            <input type="checkbox" name="is_promo" value="1" class="form-check-input" id="is_promo"
                {{ old('is_promo', $pricelist->is_promo ?? false) ? 'checked' : '' }}>

            <label for="is_promo" class="form-check-label">Promo?</label>
        </div>

        <div class="mb-3">
            <label>Effective Date</label>
            <input type="date" name="effective_date" class="form-control"
                value="{{ old('effective_date', isset($pricelist) ? date('Y-m-d', strtotime($pricelist->effective_date)) : '') }}"
                required>
        </div>


        <button class="btn btn-primary" type="submit">Update</button>
        <a href="{{ route('dashboard.pricelists.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<script>
    document.getElementById('editPricelistForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Update Confirmation',
            text: "Are you sure to update this pricelist data?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
</script>
@endsection