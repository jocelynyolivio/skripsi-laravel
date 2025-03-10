@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Laporan Laba Rugi</h3>

    <!-- Filter -->
    <form action="{{ route('dashboard.reports.income_statement') }}" method="GET" class="mb-4">
        <label>Periode:</label>
        <select name="period" class="form-select">
            <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Bulanan</option>
            <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Tahunan</option>
        </select>
        <input type="date" name="date" value="{{ $date }}" class="form-control mt-2">
        <button type="submit" class="btn btn-primary mt-2">Filter</button>
    </form>

    <!-- Pendapatan -->
    <h4>Pendapatan</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama Akun</th>
                <th>Jumlah (IDR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenues as $revenue)
            <tr>
                <td>{{ $revenue->name }}</td>
                <td>{{ number_format($revenue->saldo, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Beban Pokok Pendapatan (HPP) -->
    <h4>Beban Pokok Pendapatan (HPP)</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama Akun</th>
                <th>Jumlah (IDR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($hpp as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ number_format($item->saldo, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Beban Operasional -->
    <h4>Beban Operasional</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama Akun</th>
                <th>Jumlah (IDR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($operatingExpenses as $expense)
            <tr>
                <td>{{ $expense->name }}</td>
                <td>{{ number_format($expense->saldo, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Laba Bersih: {{ number_format($netIncome, 2) }}</h3>
</div>
@endsection
