@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Balance Sheets']
        ]
    ])
@endsection
@section('container')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Laporan Neraca</h4>
                <form action="{{ route('dashboard.reports.balance_sheet') }}" method="GET" class="row g-2 align-items-center">
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
                <p class="mb-0 text-sm">Per {{ \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y') }}</p>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <!-- Aset -->
                    <div class="table-responsive">
                        <table class="table table-sm align-items-center mb-4">
                            <thead class="bg-light">
                                <tr>
                                    <th colspan="2" class="text-uppercase text-secondary text-xxs font-weight-bolder">Aset</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assets as $coa)
                                <tr>
                                    <td class="ps-4">
                                        <p class="text-xs font-weight-bold mb-0">{{ $coa->coa_name }}</p>
                                    </td>
                                    <td class="text-end">
                                        <p class="text-xs font-weight-bold mb-0">{{ number_format($coa->total_debit - $coa->total_credit, 2) }}</p>
                                    </td>
                                </tr>
                                @endforeach
                                <tr class="bg-gray-100">
                                    <td class="ps-4">
                                        <p class="text-xs font-weight-bold mb-0">TOTAL ASET</p>
                                    </td>
                                    <td class="text-end">
                                        <p class="text-xs font-weight-bold mb-0">{{ number_format($totalAssets, 2) }}</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- Liabilitas -->
                    <div class="table-responsive">
                        <table class="table table-sm align-items-center mb-4">
                            <thead class="bg-light">
                                <tr>
                                    <th colspan="2" class="text-uppercase text-secondary text-xxs font-weight-bolder">Liabilitas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($liabilities as $coa)
                                <tr>
                                    <td class="ps-4">
                                        <p class="text-xs font-weight-bold mb-0">{{ $coa->coa_name }}</p>
                                    </td>
                                    <td class="text-end">
                                        <p class="text-xs font-weight-bold mb-0">{{ number_format($coa->total_credit - $coa->total_debit, 2) }}</p>
                                    </td>
                                </tr>
                                @endforeach
                                <tr class="bg-gray-100">
                                    <td class="ps-4">
                                        <p class="text-xs font-weight-bold mb-0">TOTAL LIABILITAS</p>
                                    </td>
                                    <td class="text-end">
                                        <p class="text-xs font-weight-bold mb-0">{{ number_format($totalLiabilities, 2) }}</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Ekuitas -->
                        <div class="table-responsive">
                            <table class="table table-sm align-items-center mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th colspan="2" class="text-uppercase text-secondary text-xxs font-weight-bolder">Ekuitas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($equities as $coa)
                                    <tr>
                                        <td class="ps-4">
                                            <p class="text-xs font-weight-bold mb-0">{{ $coa->coa_name }}</p>
                                        </td>
                                        <td class="text-end">
                                            <p class="text-xs font-weight-bold mb-0">{{ number_format($coa->total_credit - $coa->total_debit, 2) }}</p>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr class="bg-gray-100">
                                        <td class="ps-4">
                                            <p class="text-xs font-weight-bold mb-0">TOTAL EKUITAS</p>
                                        </td>
                                        <td class="text-end">
                                            <p class="text-xs font-weight-bold mb-0">{{ number_format($totalEquities, 2) }}</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Total Liabilitas + Ekuitas -->
                        <div class="table-responsive mt-4">
                            <table class="table table-sm align-items-center">
                                <tbody>
                                    <tr class="bg-gray-200">
                                        <td class="ps-4">
                                            <p class="text-xs font-weight-bold mb-0">TOTAL LIABILITAS & EKUITAS</p>
                                        </td>
                                        <td class="text-end">
                                            <p class="text-xs font-weight-bold mb-0">{{ number_format($totalLiabilities + $totalEquities, 2) }}</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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
    .bg-gray-200 {
        background-color: rgba(233, 236, 239, 0.8) !important;
    }
    .text-xxs {
        font-size: 0.65rem !important;
    }
</style>
@endsection