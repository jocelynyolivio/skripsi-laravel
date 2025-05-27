@extends('dashboard.layouts.main')
@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['text' => 'Purchase Orders', 'url' => route('dashboard.purchase_orders.index')],
['text' => 'Receive Goods from Purchase Order #'.$purchaseOrder->order_number]
]
])
@endsection
@section('container')
<div class="container mt-5 col-md-8 mx-auto">
    <div class="container text-center">
        <h3>Konfirmasi Penerimaan Barang dari Purchase Order</h3>

        @if ($purchaseOrder->status === 'received')
            <div class="alert alert-success mt-4" role="alert">
                <h4 class="alert-heading">
                    <i class="bi bi-check-circle-fill"></i> Sudah Diterima!
                </h4>
                <p>
                    Purchase Order dengan nomor <strong>#{{ $purchaseOrder->order_number }}</strong> telah selesai diproses dan semua barang telah diterima.
                </p>
                <hr>
                <p class="mb-0">
                    Tidak ada tindakan lebih lanjut yang diperlukan untuk PO ini.
                </p>
                <a href="{{ route('dashboard.purchase_orders.index') }}" class="btn btn-primary mt-3">
                    Kembali ke Daftar Purchase Order
                </a>
            </div>
        @else
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
        @endif
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
