@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['url' => route('dashboard.procedure_types.index'), 'text' => 'Procedure Types'],
            ['text' => 'Edit Procedure Type']
        ]
    ])
@endsection
@section('container')
<div class="container col-md-6 mt-5">
    <h3>Edit Procedure Type</h3>
    <form action="{{ route('dashboard.procedure_types.update', $procedureType->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name', $procedureType->name) }}">
        </div>
        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="description" class="form-control">{{ old('description', $procedureType->description) }}</textarea>
        </div>
        <button type="submit" class="btn btn-success mt-2">Update</button>
    </form>
</div>
@endsection
