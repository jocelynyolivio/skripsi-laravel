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
    <div class="card rounded">
        <div class="card-body p-4">
            <h3 class="mb-4">Edit Procedure</h3>
            <form action="{{ route('dashboard.procedures.update', $procedure->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group mb-3">
                    <label for="item_code">Item Code</label>
                    <input type="text" id="item_code" name="item_code" class="form-control" value="{{ $procedure->item_code }}" readonly>
                </div>

                <div class="form-group mb-3">
                    <label for="name">Procedure Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ $procedure->name }}" required>
                </div>

                <div class="form-group mb-3">
                    <label for="procedure_type_id">Procedure Type</label>
                    <select id="procedure_type_id" name="procedure_type_id" class="form-control">
                        <option value="">-</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" {{ $procedure->procedure_type_id == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3">{{ $procedure->description }}</textarea>
                </div>

                <div class="form-group mb-4">
                    <label for="requires_tooth">Requires Tooth?</label>
                    <select id="requires_tooth" name="requires_tooth" class="form-control">
                        <option value="1" {{ $procedure->requires_tooth ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ !$procedure->requires_tooth ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success btn-lg w-100">Update Procedure</button>
            </form>
        </div>
    </div>
</div>
@endsection
