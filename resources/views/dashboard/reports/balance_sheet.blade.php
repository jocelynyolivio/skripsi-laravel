@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Laporan Neraca</h3>

    <!-- Filter -->
    <form action="{{ route('dashboard.reports.balance_sheet') }}" method="GET" class="mb-4">
        <label>Periode:</label>
        <select name="period" class="form-select">
            <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Bulanan</option>
            <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Tahunan</option>
        </select>
        <input type="date" name="date" value="{{ $date }}" class="form-control mt-2">
        <button type="submit" class="btn btn-primary mt-2">Filter</button>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>COA ID</th>
                <th>Total Debit</th>
                <th>Total Credit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($coaSummary as $coa)
            <tr>
                <td>{{ $coa->coa_name }}</td>
                <td>{{ number_format($coa->total_debit, 2) }}</td>
                <td>{{ number_format($coa->total_credit, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
