@extends('dashboard.layouts.main')
@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
    'customBreadcrumbs' => [
        ['text' => 'Purchase Invoices', 'url' => route('dashboard.purchases.index')], // Item breadcrumb pertama
        ['text' => 'Receive goods for invoice ' . $purchase->id] // Item breadcrumb kedua (halaman saat ini)
    ]
])
@endsection
@section('container')
<div class="container mt-5 col-md-8 mx-auto">
    <div class="container text-center">
        <h3>Konfirmasi Penerimaan Barang</h3>
        <form id="receiveForm" action="{{ route('dashboard.purchases.storeReceived', $purchase->id) }}" method="POST">
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
                            <td>{{ number_format($detail->final_unit_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="button" id="submitBtn" class="btn btn-success w-100">Simpan</button>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#submitBtn').click(function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Konfirmasi Simpan?',
            text: "Pastikan data yang dimasukkan sudah benar.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#receiveForm').submit(); // Kirim form setelah konfirmasi
            }
        });
    });
});
</script>
@endsection
