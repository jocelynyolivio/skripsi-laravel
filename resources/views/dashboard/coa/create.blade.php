@extends('dashboard.layouts.main')
    @section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
        ['url' => route('dashboard.coa.index'), 'text' => 'Chart Of Account'],
        ['text' => 'Create Chart Of Account']
        ]
        ])
@endsection
@section('container')
<div class="container">
    <h3>Create New Chart of Account</h3>
    <form action="{{ route('dashboard.coa.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="code">Code</label>
            <input type="text" name="code" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="type">Type</label>
            <select name="type" class="form-control" required>
                <option value="">-- Select Type --</option>
                <option value="asset">Asset</option>
                <option value="liability">Liability</option>
                <option value="equity">Equity</option>
                <option value="expense">Expense</option>
            </select>
        </div>

        <button class="btn btn-primary">Save</button>
        <a href="{{ route('dashboard.coa.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection