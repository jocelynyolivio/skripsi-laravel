@extends('dashboard.layouts.main')

@section('container')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Laporan Arus Kas</h4>
                <form action="{{ route('dashboard.reports.cash_flow') }}" method="GET" class="row g-2 align-items-center">
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
            <!-- Saldo Awal -->
            <div class="row mb-4">
                <div class="col-md-6 offset-md-6">
                    <table class="table table-sm">
                        <tr>
                            <td class="ps-4"><strong>Saldo Kas Awal</strong></td>
                            <td class="text-end">{{ number_format($beginningCashBalance, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Arus Kas Operasional -->
            <div class="table-responsive">
                <table class="table table-sm align-items-center mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th colspan="2" class="text-uppercase text-secondary text-xxs font-weight-bolder">Aktivitas Operasional</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($operatingActivities as $item)
                        @if($item->cash_in > 0)
                        <tr>
                            <td class="ps-4">
                                <p class="text-xs mb-0">{{ $item->code }} - {{ $item->name }}</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs mb-0">{{ number_format($item->cash_in, 2) }}</p>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                        <tr class="bg-gray-100">
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">Total Penerimaan Kas</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($operatingActivities->sum('cash_in'), 2) }}</p>
                            </td>
                        </tr>

                        @foreach($operatingActivities as $item)
                        @if($item->cash_out > 0)
                        <tr>
                            <td class="ps-4">
                                <p class="text-xs mb-0">{{ $item->code }} - {{ $item->name }}</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs mb-0">({{ number_format($item->cash_out, 2) }})</p>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                        <tr class="bg-gray-100">
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">Total Pengeluaran Kas</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs font-weight-bold mb-0">({{ number_format($operatingActivities->sum('cash_out'), 2) }})</p>
                            </td>
                        </tr>
                        <tr class="border-top">
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">Arus Kas Bersih dari Operasi</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($netOperating, 2) }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Arus Kas Investasi -->
            <div class="table-responsive mt-4">
                <table class="table table-sm align-items-center mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th colspan="2" class="text-uppercase text-secondary text-xxs font-weight-bolder">Aktivitas Investasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($investmentActivities as $item)
                        @if($item->cash_in > 0)
                        <tr>
                            <td class="ps-4">
                                <p class="text-xs mb-0">{{ $item->code }} - {{ $item->name }}</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs mb-0">{{ number_format($item->cash_in, 2) }}</p>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                        <tr class="bg-gray-100">
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">Total Penerimaan Kas</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($investmentActivities->sum('cash_in'), 2) }}</p>
                            </td>
                        </tr>

                        @foreach($investmentActivities as $item)
                        @if($item->cash_out > 0)
                        <tr>
                            <td class="ps-4">
                                <p class="text-xs mb-0">{{ $item->code }} - {{ $item->name }}</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs mb-0">({{ number_format($item->cash_out, 2) }})</p>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                        <tr class="bg-gray-100">
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">Total Pengeluaran Kas</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs font-weight-bold mb-0">({{ number_format($investmentActivities->sum('cash_out'), 2) }})</p>
                            </td>
                        </tr>
                        <tr class="border-top">
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">Arus Kas Bersih dari Investasi</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($netInvesting, 2) }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Arus Kas Pendanaan -->
            <div class="table-responsive mt-4">
                <table class="table table-sm align-items-center mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th colspan="2" class="text-uppercase text-secondary text-xxs font-weight-bolder">Aktivitas Pendanaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($financingActivities as $item)
                        @if($item->cash_in > 0)
                        <tr>
                            <td class="ps-4">
                                <p class="text-xs mb-0">{{ $item->code }} - {{ $item->name }}</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs mb-0">{{ number_format($item->cash_in, 2) }}</p>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                        <tr class="bg-gray-100">
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">Total Penerimaan Kas</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($financingActivities->sum('cash_in'), 2) }}</p>
                            </td>
                        </tr>

                        @foreach($financingActivities as $item)
                        @if($item->cash_out > 0)
                        <tr>
                            <td class="ps-4">
                                <p class="text-xs mb-0">{{ $item->code }} - {{ $item->name }}</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs mb-0">({{ number_format($item->cash_out, 2) }})</p>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                        <tr class="bg-gray-100">
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">Total Pengeluaran Kas</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs font-weight-bold mb-0">({{ number_format($financingActivities->sum('cash_out'), 2) }})</p>
                            </td>
                        </tr>
                        <tr class="border-top">
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">Arus Kas Bersih dari Pendanaan</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($netFinancing, 2) }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Total dan Saldo Akhir -->
            <div class="row mt-4">
                <div class="col-md-6 offset-md-6">
                    <table class="table table-sm">
                        <tr class="border-top-2">
                            <td class="ps-4"><strong>Kenaikan/Penurunan Kas Bersih</strong></td>
                            <td class="text-end">{{ number_format($netCashFlow, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4"><strong>Saldo Kas Awal</strong></td>
                            <td class="text-end">{{ number_format($beginningCashBalance, 2) }}</td>
                        </tr>
                        <tr class="bg-primary text-white">
                            <td class="ps-4"><strong>Saldo Kas Akhir</strong></td>
                            <td class="text-end"><strong>{{ number_format($endingCashBalance, 2) }}</strong></td>
                        </tr>
                    </table>
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
    .text-xxs {
        font-size: 0.65rem !important;
    }
    .border-top {
        border-top: 1px solid #dee2e6 !important;
    }
    .border-top-2 {
        border-top: 2px solid #dee2e6 !important;
    }
</style>
@endsection