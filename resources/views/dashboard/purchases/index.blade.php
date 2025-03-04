@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">

    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Purchase Invoices List</h3>
        <a href="{{ route('dashboard.purchases.create') }}" class="btn btn-primary">Add Purchase</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="purchasesTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Supplier</th>
                <th>Purchase Date</th>
                <th>Total Amount</th>
                <th>Total Debt</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchases as $purchase)
            <tr>
                <td>{{ $purchase->supplier->nama ?? '-' }}</td>
                <td>{{ $purchase->purchase_date }}</td>
                <td>Rp. {{ number_format($purchase->total_amount, 2, ',', '.') }}</td>
                <td>Rp. {{ number_format($purchase->latestPayment->total_debt ?? 0, 2, ',', '.') }}</td>

                <td>{{ ucfirst($purchase->status) }}</td>
                <td>
                    <a href="{{ route('dashboard.purchases.edit', $purchase->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('dashboard.purchases.destroy', $purchase->id) }}" method="POST" class="d-inline delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm delete-button">Delete</button>
                    </form>

                    <a href="{{ route('dashboard.purchases.receive', $purchase->id) }}" class="btn btn-primary">
                        Terima Barang
                    </a>


                    @if ($purchase->latestPayment && $purchase->latestPayment->total_debt > 0)
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#payDebtModal-{{ $purchase->id }}">
                        Add Purchase Payment
                    </button>

                    <!-- Modal untuk Pembayaran Hutang -->
                    <div class="modal fade" id="payDebtModal-{{ $purchase->id }}" tabindex="-1" aria-labelledby="payDebtModalLabel-{{ $purchase->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('dashboard.purchases.payDebt') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="purchase_id" value="{{ $purchase->id }}">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Add Purchase Payment</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Payment Amount</label>
                                            <input type="number" class="form-control" name="amount" min="1" max="{{ $purchase->latestPayment->total_debt }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Payment Date</label>
                                            <input type="date" class="form-control" name="payment_date" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="coa_id">Bayar Dari (Akun Kas/Bank)</label>
                                            <select class="form-control" id="coa_id" name="coa_id" required>
                                                <option value="">-- Pilih Akun Kas/Bank --</option>
                                                @foreach ($coa as $account)
                                                <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Notes</label>
                                            <textarea class="form-control" name="notes"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">Submit Payment</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- DataTables & SweetAlert -->
<script>
    $(document).ready(function() {
        $('#purchasesTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
        });
    });

    // SweetAlert untuk Konfirmasi Hapus
    $('#purchasesTable').on('click', '.delete-button', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endsection