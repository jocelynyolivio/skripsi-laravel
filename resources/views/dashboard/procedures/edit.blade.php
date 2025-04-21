@extends('dashboard.layouts.main')
@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
    'customBreadcrumbs' => [
        ['url' => route('dashboard.procedures.index'), 'text' => 'Procedures'],
        ['text' => 'Edit Procedure']
    ]
])
@endsection
@section('container')
<div class="container mt-5 col-md-6">
    <h2>Edit Procedure</h2>
    <form action="{{ route('dashboard.procedures.update', $procedure->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Item Code</label>
            <input type="text" name="item_code" class="form-control" value="{{ $procedure->item_code }}" readonly>
        </div>
        <div class="form-group">
            <label>Procedure Name</label>
            <input type="text" name="name" class="form-control" value="{{ $procedure->name }}" required>
        </div>
        <div class="form-group">
            <label>Procedure Type</label>
            <select name="procedure_type_id" class="form-control">
                <option value="">-</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" {{ $procedure->procedure_type_id == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control">{{ $procedure->description }}</textarea>
        </div>
        <div class="form-group">
            <label>Requires Tooth?</label>
            <select name="requires_tooth" class="form-control">
                <option value="1" {{ $procedure->requires_tooth ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ !$procedure->requires_tooth ? 'selected' : '' }}>No</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
    </form>
</div>
@endsection
