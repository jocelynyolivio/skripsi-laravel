@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Income Statement']
        ]
    ])
@endsection
@section('container')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Laporan Laba Rugi</h4>
                <form action="{{ route('dashboard.reports.income_statement') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-auto">
                        <select name="period" class="form-select form-select-sm">
                            <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                            <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="date" value="{{ $date }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-primary">Tampilkan</button>
                    </div>
                </form>
            </div>
            <hr class="mt-2 mb-3">
            <div class="text-center">
                <h5 class="mb-1">SenyumQu Dental Clinic</h5>
                <p class="mb-0 text-sm">
                    Periode: {{ $period == 'monthly' ? 
                        Carbon\Carbon::parse($date)->isoFormat('MMMM Y') : 
                        Carbon\Carbon::parse($date)->isoFormat('Y') }}
                </p>
            </div>
        </div>

        <div class="card-body">
            <!-- Pendapatan -->
            <div class="table-responsive">
                <table class="table table-sm align-items-center mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Pendapatan</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-end">Jumlah (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($revenues as $item)
                        <tr>
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">{{ $item->code }} - {{ $item->name }}</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($item->amount, 2) }}</p>
                            </td>
                        </tr>
                        @endforeach
                        <tr class="bg-gray-100">
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">Total Pendapatan</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($totalRevenue, 2) }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- HPP -->
            <div class="table-responsive mt-4">
                <table class="table table-sm align-items-center mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Beban Pokok Pendapatan</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-end">Jumlah (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hpp as $item)
                        <tr>
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">{{ $item->code }} - {{ $item->name }}</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($item->amount, 2) }}</p>
                            </td>
                        </tr>
                        @endforeach
                        <tr class="bg-gray-100">
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">Total Beban Pokok Pendapatan</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($totalHpp, 2) }}</p>
                            </td>
                        </tr>
                        <tr class="border-top">
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">Laba Kotor</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($grossProfit, 2) }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Beban Operasional -->
            <div class="table-responsive mt-4">
                <table class="table table-sm align-items-center mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Beban Operasional</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-end">Jumlah (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($operatingExpenses as $item)
                        <tr>
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">{{ $item->code }} - {{ $item->name }}</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($item->amount, 2) }}</p>
                            </td>
                        </tr>
                        @endforeach
                        <tr class="bg-gray-100">
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">Total Beban Operasional</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($totalOperatingExpenses, 2) }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Laba Bersih -->
            <div class="table-responsive mt-4">
                <table class="table table-sm align-items-center">
                    <tbody>
                        <tr class="bg-primary text-white">
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">LABA BERSIH</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($netIncome, 2) }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-light {
        background-color: #f8f9fa !important;
    }
    .bg-gray-100 {
        background-color: rgba(248, 249, 250, 0.8) !important;
    }
    .text-xxs {
        font-size: 0.65rem !important;
    }
    .border-top {
        border-top: 2px solid #dee2e6 !important;
    }
</style>
@endsection