@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3>Journal Entries</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Transaction ID</th>
                <th>Date</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($journals as $journal)
            <tr>
                <td>{{ $journal->id }}</td>
                <td>{{ $journal->transaction_id }}</td>
                <td>{{ $journal->entry_date }}</td>
                <td>{{ $journal->description }}</td>
                <td>
                    <a href="{{ route('dashboard.journals.show', $journal->id) }}" class="btn btn-info">Details</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
