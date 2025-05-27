@extends('dashboard.layouts.main')
@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['url' => route('dashboard.holidays.index'), 'text' => 'Holidays'],
['text' => 'Create Holidays']
]
])
@endsection
@section('container')
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <h2>Create Holidays</h2>
    <form action="{{ route('dashboard.holidays.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Date</label>
            <input type="date" name="tanggal" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <input type="text" name="keterangan" class="form-control" required>
        </div>
        <br>
        <button type="submit" class="btn btn-success">Save</button>
    </form>
</div>
@endsection