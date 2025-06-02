@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Journal Entries', 'url' => route('dashboard.journals.index')],
            ['text' => 'Create Journal']
        ]
    ])
@endsection
@section('container')
<div class="container mt-5 col-md-6">
    <h2>Create Journal</h2>

    <form action="{{ route('dashboard.journals.store') }}" method="POST" id="createJournalForm">
        @csrf

        <div class="mb-3">
            <label for="entry_date">Tanggal</label>
            <input type="date" name="entry_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="description">Deskripsi</label>
            <textarea name="description" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label for="coa_in">COA (Debit)</label>
            <select name="coa_in" class="form-control" required>
                <option value="">-- Pilih Akun Debit --</option>
                @foreach($coas as $coa)
                    <option value="{{ $coa->id }}">{{ $coa->code }} - {{ $coa->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="coa_out">COA (Kredit)</label>
            <select name="coa_out" class="form-control" required>
                <option value="">-- Pilih Akun Kredit --</option>
                @foreach($coas as $coa)
                    <option value="{{ $coa->id }}">{{ $coa->code }} - {{ $coa->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="amount">Jumlah</label>
            <input type="number" name="amount" class="form-control" step="0.01" required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
<script>
    document.getElementById('createJournalForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Confirm Journal',
            text: "Are you sure you want to create this journal?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, sure!'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
</script>
@endsection
