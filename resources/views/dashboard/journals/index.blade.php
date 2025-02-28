@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Journal Entries</h3>

    <table class="table table-bordered">
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
                            Lihat Detail
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
