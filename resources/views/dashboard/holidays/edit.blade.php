@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['url' => route('dashboard.holidays.index'), 'text' => 'Holidays'],
            ['text' => 'Edit Holidays']
        ]
    ])
@endsection
@section('container')
<div class="container mt-5 col-md-6">
    <h2>Edit Holidays</h2>
    <form action="{{ route('dashboard.holidays.update', $holiday->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Date</label>
            <input type="date" name="tanggal" class="form-control" value="{{ $holiday->tanggal }}" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <input type="text" name="keterangan" class="form-control" value="{{ $holiday->keterangan }}" required>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
    </form>
</div>
@endsection
