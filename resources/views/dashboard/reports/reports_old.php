<!-- ini yang lama pol. yang salah ada debit credit -->
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


<!-- ini yang mirip mekari --> 
<!-- ws kebagi aset liability sm equity -->
@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Laporan Neraca</h3>

    <form action="{{ route('dashboard.reports.balance_sheet') }}" method="GET" class="mb-4">
        <label>Periode:</label>
        <select name="period" class="form-select">
            <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Bulanan</option>
            <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Tahunan</option>
        </select>
        <input type="date" name="date" value="{{ $date }}" class="form-control mt-2">
        <button type="submit" class="btn btn-primary mt-2">Filter</button>
    </form>

    <div class="row">
        <div class="col-md-6">
            <h5>Aset</h5>
            <table class="table table-sm">
                @foreach($assets as $coa)
                <tr>
                    <td>{{ $coa->coa_name }}</td>
                    <td class="text-end">{{ number_format($coa->total_debit - $coa->total_credit, 2) }}</td>
                </tr>
                @endforeach
                <tr class="fw-bold">
                    <td>Total Aset</td>
                    <td class="text-end">{{ number_format($totalAssets, 2) }}</td>
                </tr>
            </table>

            <h5 class="mt-4">Liabilitas</h5>
            <table class="table table-sm">
                @foreach($liabilities as $coa)
                <tr>
                    <td>{{ $coa->coa_name }}</td>
                    <td class="text-end">{{ number_format($coa->total_credit - $coa->total_debit, 2) }}</td>
                </tr>
                @endforeach
                <tr class="fw-bold">
                    <td>Total Liabilitas</td>
                    <td class="text-end">{{ number_format($totalLiabilities, 2) }}</td>
                </tr>
            </table>

            <h5 class="mt-4">Ekuitas</h5>
            <table class="table table-sm">
                @foreach($equities as $coa)
                <tr>
                    <td>{{ $coa->coa_name }}</td>
                    <td class="text-end">{{ number_format($coa->total_credit - $coa->total_debit, 2) }}</td>
                </tr>
                @endforeach
                <tr class="fw-bold">
                    <td>Total Ekuitas</td>
                    <td class="text-end">{{ number_format($totalEquities, 2) }}</td>
                </tr>
            </table>

            <hr>
            <table class="table table-sm">
                <tr class="fw-bold">
                    <td>Total Liabilitas + Ekuitas</td>
                    <td class="text-end">{{ number_format($totalLiabilities + $totalEquities, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection

<!-- income statement lama -->
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

<!-- cash flow lama -->
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
    <h4>Arus Kas dari Aktivitas Operasi</h4>
    <p>Kas Masuk: {{ number_format($operatingCashIn, 2) }}</p>
    <p>Kas Keluar: {{ number_format($operatingCashOut, 2) }}</p>
    <p><strong>Kas Bersih: {{ number_format($operatingCashIn - $operatingCashOut, 2) }}</strong></p>

    <h4>Arus Kas dari Aktivitas Investasi</h4>
    <p>Kas Masuk: {{ number_format($investmentCashIn, 2) }}</p>
    <p>Kas Keluar: {{ number_format($investmentCashOut, 2) }}</p>
    <p><strong>Kas Bersih: {{ number_format($investmentCashIn - $investmentCashOut, 2) }}</strong></p>

    <h4>Arus Kas dari Aktivitas Pendanaan</h4>
    <p>Kas Masuk: {{ number_format($financingCashIn, 2) }}</p>
    <p>Kas Keluar: {{ number_format($financingCashOut, 2) }}</p>
    <p><strong>Kas Bersih: {{ number_format($financingCashIn - $financingCashOut, 2) }}</strong></p>

    <h3>Total Arus Kas Bersih**: {{ number_format($netCashFlow, 2) }}</h3>

</div>
@endsection