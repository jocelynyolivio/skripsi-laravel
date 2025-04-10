{{-- resources/views/dashboard/dental_materials/edit.blade.php --}}
@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-6">
    <h3 class="text-center">Edit Dental Material</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('dashboard.dental-materials.update', $dentalMaterial->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Material Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $dentalMaterial->name }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description">{{ $dentalMaterial->description }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update Material</button>
    </form>
</div>
@endsection
