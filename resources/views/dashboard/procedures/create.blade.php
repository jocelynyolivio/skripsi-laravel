@extends('dashboard.layouts.main')
@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
    'customBreadcrumbs' => [
        ['url' => route('dashboard.procedures.index'), 'text' => 'Procedures'],
        ['text' => 'Create Procedure']
    ]
])
@endsection
@section('container')
<div class="container mt-5 col-md-6">
    <h2>Create Procedure</h2>
    <form action="{{ route('dashboard.procedures.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Procedure Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Procedure Type</label>
            <select name="procedure_type_id" class="form-control">
                <option value="">-</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label>Requires Tooth?</label>
            <select name="requires_tooth" class="form-control">
                <option value="1" selected>Yes</option>
                <option value="0">No</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
