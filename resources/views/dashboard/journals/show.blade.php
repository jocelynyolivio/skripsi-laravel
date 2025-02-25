@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3>Journal Details</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Account</th>
                <th>Debit</th>
                <th>Credit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($journal->details as $detail)
            <tr>
                <td>{{ $detail->account->name }}</td>
                <td>{{ number_format($detail->debit, 2, ',', '.') }}</td>
                <td>{{ number_format($detail->credit, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
