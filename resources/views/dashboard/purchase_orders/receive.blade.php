@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-8 mx-auto">
    <div class="container text-center">
        <h3>Konfirmasi Penerimaan Barang dari Purchase Order</h3>
        <form id="receiveForm" action="{{ route('dashboard.purchase_orders.storeReceived', $purchaseOrder->id) }}" method="POST">
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
                    @foreach ($purchaseOrder->details as $detail)
                        <tr>
                            <td>{{ $detail->material->name }}</td>
                            <td>{{ $detail->quantity }}</td>
                            <td>
                                <input type="number" name="received_quantity[{{ $detail->material_id }}]" value="{{ $detail->quantity }}" min="0" class="form-control">
                            </td>
                            <td>{{ number_format($detail->price, 2) }}</td>
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
                $('#receiveForm').submit();
            }
        });
    });
});
</script>
@endsection
