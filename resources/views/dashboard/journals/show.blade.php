@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Journal Details</h3>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Account</th>
                <th>Debit</th>
                <th>Credit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($journalDetails as $detail)
                <tr>
                    <td>{{ $detail->account->name }}</td>
                    <td>{{ number_format($detail->debit, 2) }}</td>
                    <td>{{ number_format($detail->credit, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($isHPP)
    <h3 class="text-center mt-5">Rincian HPP</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama Bahan</th>
                <th>Jumlah yang Digunakan</th>
                <th>Harga per Unit</th>
                <th>Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($hppDetails as $detail)
                <tr>
                    <td>{{ $detail['name'] }}</td>
                    <td>{{ $detail['quantity'] }}</td>
                    <td>Rp. {{ number_format($detail['unit_price'], 2) }}</td>
                    <td>Rp. {{ number_format($detail['total_price'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total HPP</th>
                <th>Rp. {{ number_format($totalHPP, 2) }}</th>
            </tr>
        </tfoot>
    </table>
    @endif
</div>
@endsection
