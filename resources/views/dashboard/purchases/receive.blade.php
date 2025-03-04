@extends('dashboard.layouts.main')

@section('container')
<div class="container">
    <h3>Konfirmasi Penerimaan Barang</h3>
    <form action="{{ route('dashboard.purchases.storeReceived', $purchase->id) }}" method="POST">
        @csrf
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Material</th>
                    <th>Quantity Dipesan</th>
                    <th>Quantity Diterima</th>
                    <th>Harga Satuan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchase->details as $detail)
                    <tr>
                        <td>{{ $detail->material->name }}</td>
                        <td>{{ $detail->quantity }} {{ $detail->unit }}</td>
                        <td>
                            <input type="number" name="received_quantity[{{ $detail->dental_material_id }}]" value="{{ $detail->quantity }}" min="0" class="form-control">
                        </td>
                        <td>{{ number_format($detail->unit_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
