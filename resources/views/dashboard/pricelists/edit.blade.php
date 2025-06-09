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

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

    <form action="{{ route('dashboard.pricelists.update', $pricelist) }}" id="editPricelistForm" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Procedure <span class="text-danger">*</span></label>
            <select name="procedure_id" class="form-control" required>
                <option value="">-- Select Procedure --</option>
                @foreach($procedures as $proc)
                <option value="{{ $proc->id }}" {{ (old('procedure_id') ?? $pricelist->procedure_id) == $proc->id ? 'selected' : '' }}>{{ $proc->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Price <span class="text-danger">*</span></label>
            <input type="number" name="price" class="form-control" value="{{ old('price') ?? $pricelist->price }}" required>
        </div>

        <div class="mb-3 form-check">

            <input type="checkbox" name="is_promo" value="1" class="form-check-input" id="is_promo"
                {{ old('is_promo', $pricelist->is_promo ?? false) ? 'checked' : '' }}>

            <label for="is_promo" class="form-check-label">Promo?</label>
        </div>

        <div class="mb-3">
            <label>Effective Date</label>
            <input type="date" name="effective_date" class="form-control"
                value="{{ old('effective_date', isset($pricelist) ? date('Y-m-d', strtotime($pricelist->effective_date)) : '') }}">
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button class="btn btn-primary" type="submit">Update</button>
            <a href="{{ route('dashboard.pricelists.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
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
            confirmButtonText: 'Yes, sure!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
</script>
@endsection