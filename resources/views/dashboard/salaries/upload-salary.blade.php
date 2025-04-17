@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Salaries', 'url' => route('dashboard.salaries.index')],
            ['text' => 'Upload Attendance File']
        ]
    ])
@endsection
@section('container')
<div class="container mt-5">
    <h1 class="text-center">Upload Attendance File</h1>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form action="{{ url('dashboard/salaries/process-salary') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <input type="file" name="file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Upload</button>
            </form>
        </div>
    </div>
</div>
@endsection
