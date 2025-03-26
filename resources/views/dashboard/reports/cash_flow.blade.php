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
    <h4>🔹 **Arus Kas dari Aktivitas Operasi**</h4>
    <p>Kas Masuk: {{ number_format($operatingCashIn, 2) }}</p>
    <p>Kas Keluar: {{ number_format($operatingCashOut, 2) }}</p>
    <p><strong>Kas Bersih: {{ number_format($operatingCashIn - $operatingCashOut, 2) }}</strong></p>

    <h4>🔹 **Arus Kas dari Aktivitas Investasi**</h4>
    <p>Kas Masuk: {{ number_format($investmentCashIn, 2) }}</p>
    <p>Kas Keluar: {{ number_format($investmentCashOut, 2) }}</p>
    <p><strong>Kas Bersih: {{ number_format($investmentCashIn - $investmentCashOut, 2) }}</strong></p>

    <h4>🔹 **Arus Kas dari Aktivitas Pendanaan**</h4>
    <p>Kas Masuk: {{ number_format($financingCashIn, 2) }}</p>
    <p>Kas Keluar: {{ number_format($financingCashOut, 2) }}</p>
    <p><strong>Kas Bersih: {{ number_format($financingCashIn - $financingCashOut, 2) }}</strong></p>

    <h3>✅ **Total Arus Kas Bersih**: {{ number_format($netCashFlow, 2) }}</h3>

</div>
@endsection