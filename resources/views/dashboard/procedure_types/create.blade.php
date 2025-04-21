@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['url' => route('dashboard.procedure_types.index'), 'text' => 'Procedure Types'],
            ['text' => 'Create Procedure Type']
        ]
    ])
@endsection
@section('container')
<div class="container col-md-6 mt-5">
    <h3>Create Procedure Type</h3>
    <form action="{{ route('dashboard.procedure_types.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
        </div>
        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>
        <button type="submit" class="btn btn-success mt-2">Simpan</button>
    </form>
</div>
@endsection
