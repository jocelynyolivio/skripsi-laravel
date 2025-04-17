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
<div class="container mt-5 col-md-6">
    <h2>Create Holidays</h2>
    <form action="{{ route('dashboard.holidays.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Keterangan</label>
            <input type="text" name="keterangan" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
