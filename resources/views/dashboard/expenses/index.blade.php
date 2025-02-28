@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">

    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Expenses List</h3>
        <a href="{{ route('dashboard.expenses.create') }}" class="btn btn-primary">Add Expense</a>
    </div>

    <table id="expensesTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Supplier</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Category</th>
                <th>Description</th>
                <th>Material</th>
                <th>Quantity</th>
                <th>Expired at</th>
                <th>Created By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($expenses as $expense)
            <tr>
                <td>{{ $expense->supplier->nama ?? '-' }}</td>
                <td>{{ $expense->date }}</td>
                <td>Rp. {{ number_format($expense->amount, 2, ',', '.') }}</td>
                <td>{{ $expense->category->name }}</td>
                <td>{{ $expense->description }}</td>
                <td>{{ $expense->dentalMaterial?->name ?? '-' }}</td>
                <td>{{ $expense->quantity ?? '-' }}</td>
                <td>{{ $expense->expired_at ? date('d M Y', strtotime($expense->expired_at)) : '-' }}</td>
                <td>{{ $expense->admin?->name ?? 'N/A' }}</td>
                <td>
                    <!-- Action Buttons -->
                    <a href="{{ route('dashboard.expenses.edit', $expense->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <!-- <form action="{{ route('dashboard.expenses.destroy', $expense->id) }}" method="POST" style="display:inline;" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger delete-button">Delete</button>
                    </form> -->

                    <!-- Loop untuk Purchases yang Masih Ada Hutang -->
                    @foreach ($expense->purchases as $purchase)
                        @if ($purchase->total_debt > 0)
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#payDebtModal-{{ $purchase->id }}">
                            Bayar Hutang
                        </button>

                        <!-- Modal Bayar Hutang -->
                        <div class="modal fade" id="payDebtModal-{{ $purchase->id }}" tabindex="-1" aria-labelledby="payDebtModalLabel-{{ $purchase->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('dashboard.purchases.pay', $purchase->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="payDebtModalLabel-{{ $purchase->id }}">Bayar Hutang - Purchase ID: {{ $purchase->id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="amount-{{ $purchase->id }}" class="form-label">Jumlah Pembayaran</label>
                                                <input type="number" class="form-control" id="amount-{{ $purchase->id }}" name="amount" min="1" max="{{ $purchase->total_debt }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="coa_id-{{ $purchase->id }}">Bayar Dari (Akun Kas/Bank)</label>
                                                <select class="form-control" id="coa_id-{{ $purchase->id }}" name="coa_id" required>
                                                    <option value="">-- Pilih Akun Kas/Bank --</option>
                                                    @foreach ($coa as $account)
                                                    <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="payment_date-{{ $purchase->id }}" class="form-label">Tanggal Pembayaran</label>
                                                <input type="date" class="form-control" id="payment_date-{{ $purchase->id }}" name="payment_date" value="{{ date('Y-m-d') }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="notes-{{ $purchase->id }}" class="form-label">Catatan</label>
                                                <textarea class="form-control" id="notes-{{ $purchase->id }}" name="notes"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Bayar Hutang</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- DataTables & SweetAlert -->
<script>
    $(document).ready(function() {
        $('#expensesTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
        });
    });

    // SweetAlert untuk Konfirmasi Hapus
    $('#expensesTable').on('click', '.delete-button', function(e) {
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
