@extends('dashboard.layouts.main')
@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['text' => 'Profile', 'url' => route('dashboard.profile.index')],
['text' => 'Attendance and Slips']
]
])
@endsection
@section('container')
<div class="container mt-4">

    <h3>Hi, {{ $userLogged->name }}</h3>

    <form method="GET" action="{{ route('dashboard.salaries.slips') }}" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="month" class="form-label">Select Month:</label>
                <select name="month" id="month" class="form-select">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" 
                            {{ request('month', now()->format('m')) == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-md-4">
                <label for="year" class="form-label">Select Year:</label>
                <select name="year" id="year" class="form-select">
                    @for ($y = now()->year - 5; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ request('year', now()->format('Y')) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    @if ($gaji)
        <h4>Slip Gaji Bulan {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</h4>
        <table class="table table-bordered">
                <tr><th>Normal Shift</th><td>{{ $gaji->normal_shift }}</td></tr>
                <tr><th>Holiday Shift</th><td>{{ $gaji->holiday_shift }}</td></tr>
                <tr><th>Shift Pagi</th><td>{{ $gaji->shift_pagi }}</td></tr>
                <tr><th>Shift Siang</th><td>{{ $gaji->shift_siang }}</td></tr>
                <tr><th>Lembur</th><td>{{ $gaji->lembur }}</td></tr>
                <tr><th>Gaji Pokok</th><td>Rp {{ number_format($gaji->base_salary, 0, ',', '.') }}</td></tr>
                <tr><th>Tunjangan</th><td>Rp {{ number_format($gaji->allowance, 0, ',', '.') }}</td></tr>
            <tr><th><strong>Grand Total</strong></th><td><strong>Rp {{ number_format($gaji->grand_total, 0, ',', '.') }}</strong></td></tr>
        </table>
    @else
        <p class="text-danger">No salaries data found.</p>
    @endif

    @if(auth()->user()?->role?->role_name === 'dokter luar' || auth()->user()?->role?->role_name === 'dokter tetap' )
    @if ($bagi_hasil->isNotEmpty())
        <h4>Detail Bagi Hasil - Bulan {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Procedure ID</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total Price</th>
                    <th>Discount</th>
                    <th>Final Price</th>
                    <th>Revenue %</th>
                    <th>Revenue Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bagi_hasil as $item)
                    <tr>
                        <td>{{ $item->procedure_id }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->discount, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->final_price, 0, ',', '.') }}</td>
                        <td>{{ $item->revenue_percentage }}%</td>
                        <td>Rp {{ number_format($item->revenue_amount, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-danger">No revenue percentage data found.</p>
    @endif
    @endif

</div>
@endsection
