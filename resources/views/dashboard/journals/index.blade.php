@extends('dashboard.layouts.main')
@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['text' => 'Journal Entries']
]
])
@endsection
@section('container')
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Journal Entry</h3>
        @if(auth()->user()?->role?->role_name === 'manager')
        <a href="{{ route('dashboard.journals.create') }}" class="btn btn-primary">Create New Journal</a>
        @endif
    </div>

       @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <table id="journalTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Description</th>
                <th>Entry Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($journals as $journal)
            <tr>
                <td>{{ $journal->id }}</td>
                <td>{{ $journal->description }}</td>
                <td>{{ $journal->entry_date }}</td>
                <td>
                    <a href="{{ route('dashboard.journals.show', ['id' => $journal->id]) }}" class="btn btn-info">
                        Details </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#journalTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
        });
    });
</script>
@endsection