@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Laporan Arus Kas</h3>

    <!-- Filter -->
    <form action="{{ route('dashboard.reports.cash_flow') }}" method="GET" class="mb-4">
        <label>Periode:</label>
        <select name="period" class="form-select">
            <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Bulanan</option>
            <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Tahunan</option>
        </select>
        <input type="date" name="date" value="{{ $date }}" class="form-control mt-2">
        <button type="submit" class="btn btn-primary mt-2">Filter</button>
    </form>

    <h4>Kas Bersih dari Operasional: {{ number_format($operatingCashIn - $operatingCashOut, 2) }}</h4>
    <h4>Kas Bersih dari Investasi: {{ number_format($investmentCashIn - $investmentCashOut, 2) }}</h4>
    <h4>Kas Bersih dari Pendanaan: {{ number_format($financingCashIn - $financingCashOut, 2) }}</h4>

    <h3>Total Arus Kas Bersih: {{ number_format($netCashFlow, 2) }}</h3>
</div>
@endsection
