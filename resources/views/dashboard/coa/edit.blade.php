@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['url' => route('dashboard.coa.index'), 'text' => 'Chart Of Account'],
            ['text' => 'Edit Chart Of Account']
        ]
    ])
@endsection
@section('container')
<div class="container">
    <h3>Edit Chart of Account</h3>
    <form action="{{ route('dashboard.coa.update', $coa->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="code">Code</label>
            <input type="text" name="code" value="{{ $coa->code }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="name">Name</label>
            <input type="text" name="name" value="{{ $coa->name }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="type">Type</label>
            <input type="text" name="type" value="{{ $coa->type }}" class="form-control" required>
        </div>
        <button class="btn btn-primary">Update</button>
        <a href="{{ route('dashboard.coa.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
