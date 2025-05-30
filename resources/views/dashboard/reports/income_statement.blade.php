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
                        <input type="date" name="date" value="{{ $dateInput }}" class="form-control form-control-sm"> {{-- Ganti $date menjadi $dateInput --}}
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
                        Carbon\Carbon::parse($dateInput)->isoFormat('MMMM Y') :  // Ganti $date menjadi $dateInput
                        Carbon\Carbon::parse($dateInput)->isoFormat('Y') }}       // Ganti $date menjadi $dateInput
                </p>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm align-items-center mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Pendapatan (Kotor)</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-end">Jumlah (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grossRevenuesData as $item) {{-- Ganti $revenues --}}
                        <tr>
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">{{ $item->code }} - {{ $item->name }}</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($item->amount, 2) }}</p>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-xs">Tidak ada data pendapatan.</td>
                        </tr>
                        @endforelse
                        <tr class="bg-gray-100">
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">Total Pendapatan (Kotor)</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($totalGrossRevenue, 2) }}</p> {{-- Ganti $totalRevenue --}}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($contraRevenuesData->count() > 0)
            <div class="table-responsive mt-3">
                <table class="table table-sm align-items-center mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-4">Pengurang Pendapatan</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-end">Jumlah (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contraRevenuesData as $item)
                        <tr>
                            <td class="ps-4">
                                <p class="text-xs font-weight-normal mb-0 ms-3">{{ $item->code }} - {{ $item->name }}</p> {{-- Indentasi sedikit --}}
                            </td>
                            <td class="text-end">
                                <p class="text-xs font-weight-normal mb-0">({{ number_format($item->amount, 2) }})</p> {{-- Tampilkan sebagai pengurang --}}
                            </td>
                        </tr>
                        @endforeach
                        <tr class="bg-gray-100">
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">Total Pengurang Pendapatan</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs font-weight-bold mb-0">({{ number_format($totalContraRevenue, 2) }})</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endif

            <div class="table-responsive mt-1"> {{-- Lebih dekat jika ada kontra revenue, atau mt-3 jika tidak --}}
                <table class="table table-sm align-items-center mb-0">
                    <tbody>
                        <tr class="fw-bold" style="background-color: #e9ecef;"> {{-- Beri highlight berbeda sedikit --}}
                            <td class="ps-4">
                                <p class="text-xs mb-0">Pendapatan Bersih</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs mb-0">{{ number_format($totalNetRevenue, 2) }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>


            <div class="table-responsive mt-4">
                <table class="table table-sm align-items-center mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Beban Pokok Pendapatan</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-end">Jumlah (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hppData as $item) {{-- Ganti $hpp --}}
                        <tr>
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">{{ $item->code }} - {{ $item->name }}</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($item->amount, 2) }}</p>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-xs">Tidak ada data HPP.</td>
                        </tr>
                        @endforelse
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

            <div class="table-responsive mt-4">
                <table class="table table-sm align-items-center mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Beban Operasional</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-end">Jumlah (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($operatingExpensesData as $item) {{-- Ganti $operatingExpenses --}}
                        <tr>
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">{{ $item->code }} - {{ $item->name }}</p>
                            </td>
                            <td class="text-end">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($item->amount, 2) }}</p>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-xs">Tidak ada data beban operasional.</td>
                        </tr>
                        @endforelse
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

            <div class="table-responsive mt-4">
                <table class="table table-sm align-items-center">
                    <tbody>
                        <tr class="bg-primary text-white">
                            <td class="ps-4">
                                <p class="text-sm font-weight-bolder mb-0">LABA BERSIH</p> {{-- Buat lebih besar --}}
                            </td>
                            <td class="text-end pe-3"> {{-- Tambah padding end --}}
                                <p class="text-sm font-weight-bolder mb-0">{{ number_format($netIncome, 2) }}</p>
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
        background-color: #e9ecef !important; /* Sedikit lebih gelap dari default gray-100 bootstrap */
    }
    .text-xxs {
        font-size: 0.68rem !important; /* Sedikit adjust */
    }
    .border-top {
        border-top: 2px solid #dee2e6 !important;
    }
    .fw-bold p, p.font-weight-bolder { /* style p di dalam cell bold */
        font-weight: 600 !important;
    }
    p.font-weight-normal {
        font-weight: 400 !important;
    }
</style>
@endsection