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

    {{-- Filter Form --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Filter Journal Entries</h5>
        </div>
        <div class="card-body">
            <form id="filterJournalsForm" action="{{ route('dashboard.journals.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-5">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 me-2">Apply Filter</button>
                        <a href="{{ route('dashboard.journals.index') }}" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
                <td>{{ \Carbon\Carbon::parse($journal->entry_date)->format('d F Y') }}</td> {{-- Format tanggal --}}
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
            "order": [[2, 'desc']] // Mengurutkan berdasarkan kolom 'Entry Date' (indeks 2) secara descending
        });
    });
</script>
@endsection