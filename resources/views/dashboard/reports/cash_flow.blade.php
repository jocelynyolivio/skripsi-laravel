@extends('dashboard.layouts.main') {{-- Pastikan layout utama Anda --}}

@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Cash Flow Statement']
        ]
    ])
@endsection

@section('container')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Laporan Arus Kas</h4>
                {{-- Form untuk filter periode dan tanggal --}}
                <form action="{{ route('dashboard.reports.cash_flow') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-auto">
                        <label for="period" class="visually-hidden">Periode</label>
                        <select name="period" id="period" class="form-select form-select-sm">
                            {{-- Menggunakan variabel $period dari controller --}}
                            <option value="monthly" {{ ($period ?? 'monthly') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                            <option value="yearly" {{ ($period ?? 'monthly') == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label for="date" class="visually-hidden">Tanggal</label>
                        {{-- Menggunakan variabel $dateInput dari controller --}}
                        <input type="date" name="date" id="date" value="{{ $dateInput ?? \Carbon\Carbon::now()->format('Y-m-d') }}" class="form-control form-control-sm">
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
                    Periode:
                    @if(($period ?? 'monthly') == 'monthly')
                        {{ \Carbon\Carbon::parse($dateInput ?? \Carbon\Carbon::now())->isoFormat('MMMM YYYY') }}
                    @else
                        {{ \Carbon\Carbon::parse($dateInput ?? \Carbon\Carbon::now())->isoFormat('YYYY') }}
                    @endif
                </p>
            </div>
        </div>

        <div class="card-body">
            {{-- Saldo Awal Kas --}}
            <div class="row mb-3">
                <div class="col-md-8"></div> {{-- Spacer --}}
                <div class="col-md-4">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="ps-0 text-xs font-weight-bold">Saldo Kas dan Setara Kas, Awal Periode</td>
                            <td class="text-end text-xs font-weight-bold pe-3">{{ number_format($beginningCashBalance ?? 0, 2, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- 1. Arus Kas dari Aktivitas Operasional --}}
<div class="table-responsive mb-4">
    <table class="table table-sm align-items-center mb-0">
        <thead class="bg-light-custom">
            <tr>
                <th colspan="2" class="text-uppercase text-secondary text-xxs font-weight-bolder ps-4">Arus Kas dari Aktivitas Operasional</th>
            </tr>
        </thead>
        <tbody>
            @php $hasFlows = false; @endphp
            @forelse($operatingActivitiesData as $item)
                @if(isset($item->inferred_cash_in) && $item->inferred_cash_in > 0)
                    @php $hasFlows = true; @endphp
                    <tr>
                        <td class="ps-4">
                            {{-- Tampilkan coa_code jika ada dan relevan, atau hanya deskripsi --}}
                            <p class="text-xs font-weight-normal mb-0 ms-3">{{ $item->coa_name }}</p>
                        </td>
                        <td class="text-end pe-3">
                            <p class="text-xs font-weight-normal mb-0">{{ number_format($item->inferred_cash_in, 2, ',', '.') }}</p>
                        </td>
                    </tr>
                @endif
            @empty
                {{-- Ditangani oleh kondisi isEmpty() di bawah --}}
            @endforelse

            @forelse($operatingActivitiesData as $item)
                @if(isset($item->inferred_cash_out) && $item->inferred_cash_out > 0)
                    @php $hasFlows = true; @endphp
                    <tr>
                        <td class="ps-4">
                            <p class="text-xs font-weight-normal mb-0 ms-3">{{ $item->coa_name }}</p>
                        </td>
                        <td class="text-end pe-3">
                            <p class="text-xs font-weight-normal mb-0">({{ number_format($item->inferred_cash_out, 2, ',', '.') }})</p>
                        </td>
                    </tr>
                @endif
            @empty
                {{-- Ditangani oleh kondisi isEmpty() di bawah --}}
            @endforelse
            
            @if($operatingActivitiesData->isEmpty())
                <tr>
                    <td colspan="2" class="text-center text-xs ps-4 text-muted">Tidak ada data aktivitas operasional.</td>
                </tr>
            @endif

            <tr class="bg-gray-100-custom">
                <td class="ps-4">
                    <p class="text-xs font-weight-bold mb-0">Arus Kas Bersih dari Aktivitas Operasional</p>
                </td>
                <td class="text-end pe-3">
                    <p class="text-xs font-weight-bold mb-0">{{ number_format($netOperatingCashFlow ?? 0, 2, ',', '.') }}</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- 2. Arus Kas dari Aktivitas Investasi --}}
{{-- Ulangi pola serupa untuk $investmentActivitiesData dengan properti yang sesuai --}}
<div class="table-responsive mb-4">
    <table class="table table-sm align-items-center mb-0">
        <thead class="bg-light-custom">
            <tr>
                <th colspan="2" class="text-uppercase text-secondary text-xxs font-weight-bolder ps-4">Arus Kas dari Aktivitas Investasi</th>
            </tr>
        </thead>
        <tbody>
            @php $hasFlows = false; @endphp
            @forelse($investmentActivitiesData as $item)
                @if(isset($item->inferred_cash_in) && $item->inferred_cash_in > 0)
                    @php $hasFlows = true; @endphp
                    <tr>
                        <td class="ps-4">
                            <p class="text-xs font-weight-normal mb-0 ms-3">{{ $item->coa_name }}</p>
                        </td>
                        <td class="text-end pe-3">
                            <p class="text-xs font-weight-normal mb-0">{{ number_format($item->inferred_cash_in, 2, ',', '.') }}</p>
                        </td>
                    </tr>
                @endif
            @empty
                 {{-- Ditangani oleh kondisi isEmpty() di bawah --}}
            @endforelse
             @forelse($investmentActivitiesData as $item)
                @if(isset($item->inferred_cash_out) && $item->inferred_cash_out > 0)
                    @php $hasFlows = true; @endphp
                    <tr>
                        <td class="ps-4">
                            <p class="text-xs font-weight-normal mb-0 ms-3">{{ $item->coa_name }}</p>
                        </td>
                        <td class="text-end pe-3">
                            <p class="text-xs font-weight-normal mb-0">({{ number_format($item->inferred_cash_out, 2, ',', '.') }})</p>
                        </td>
                    </tr>
                @endif
            @empty
                 {{-- Ditangani oleh kondisi isEmpty() di bawah --}}
            @endforelse

            @if($investmentActivitiesData->isEmpty())
            <tr>
                <td colspan="2" class="text-center text-xs ps-4 text-muted">Tidak ada data aktivitas investasi.</td>
            </tr>
            @endif
            <tr class="bg-gray-100-custom">
                <td class="ps-4">
                    <p class="text-xs font-weight-bold mb-0">Arus Kas Bersih dari Aktivitas Investasi</p>
                </td>
                <td class="text-end pe-3">
                    <p class="text-xs font-weight-bold mb-0">{{ number_format($netInvestmentCashFlow ?? 0, 2, ',', '.') }}</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>


{{-- 3. Arus Kas dari Aktivitas Pendanaan --}}
{{-- Ulangi pola serupa untuk $financingActivitiesData dengan properti yang sesuai --}}
<div class="table-responsive mb-4">
    <table class="table table-sm align-items-center mb-0">
        <thead class="bg-light-custom">
            <tr>
                <th colspan="2" class="text-uppercase text-secondary text-xxs font-weight-bolder ps-4">Arus Kas dari Aktivitas Pendanaan</th>
            </tr>
        </thead>
        <tbody>
            @php $hasFlows = false; @endphp
             @forelse($financingActivitiesData as $item)
                @if(isset($item->inferred_cash_in) && $item->inferred_cash_in > 0)
                    @php $hasFlows = true; @endphp
                    <tr>
                        <td class="ps-4">
                            <p class="text-xs font-weight-normal mb-0 ms-3">{{ $item->coa_name }}</p>
                        </td>
                        <td class="text-end pe-3">
                            <p class="text-xs font-weight-normal mb-0">{{ number_format($item->inferred_cash_in, 2, ',', '.') }}</p>
                        </td>
                    </tr>
                @endif
            @empty
                 {{-- Ditangani oleh kondisi isEmpty() di bawah --}}
            @endforelse
            @forelse($financingActivitiesData as $item)
                @if(isset($item->inferred_cash_out) && $item->inferred_cash_out > 0)
                    @php $hasFlows = true; @endphp
                    <tr>
                        <td class="ps-4">
                            <p class="text-xs font-weight-normal mb-0 ms-3">{{ $item->coa_name }}</p>
                        </td>
                        <td class="text-end pe-3">
                            <p class="text-xs font-weight-normal mb-0">({{ number_format($item->inferred_cash_out, 2, ',', '.') }})</p>
                        </td>
                    </tr>
                @endif
            @empty
                 {{-- Ditangani oleh kondisi isEmpty() di bawah --}}
            @endforelse

            @if($financingActivitiesData->isEmpty())
            <tr>
                <td colspan="2" class="text-center text-xs ps-4 text-muted">Tidak ada data aktivitas pendanaan.</td>
            </tr>
            @endif
            <tr class="bg-gray-100-custom">
                <td class="ps-4">
                    <p class="text-xs font-weight-bold mb-0">Arus Kas Bersih dari Aktivitas Pendanaan</p>
                </td>
                <td class="text-end pe-3">
                    <p class="text-xs font-weight-bold mb-0">{{ number_format($netFinancingCashFlow ?? 0, 2, ',', '.') }}</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>

            {{-- Ringkasan Arus Kas dan Saldo Akhir --}}
            <div class="row mt-4">
                <div class="col-md-8"></div> {{-- Spacer --}}
                <div class="col-md-4">
                    <table class="table table-sm table-borderless">
                        <tr class="border-top-custom">
                            <td class="ps-0 text-xs font-weight-bold">Kenaikan (Penurunan) Bersih Kas dan Setara Kas</td>
                            <td class="text-end text-xs font-weight-bold pe-3">{{ number_format($netCashFlowChange ?? 0, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="ps-0 text-xs font-weight-normal">Saldo Kas dan Setara Kas, Awal Periode</td>
                            <td class="text-end text-xs font-weight-normal pe-3">{{ number_format($beginningCashBalance ?? 0, 2, ',', '.') }}</td>
                        </tr>
                        <tr class="bg-primary-custom text-white-custom highlight-final-balance">
                            <td class="ps-0 text-sm font-weight-bolder">Saldo Kas dan Setara Kas, Akhir Periode</td>
                            <td class="text-end text-sm font-weight-bolder pe-3">{{ number_format($endingCashBalance ?? 0, 2, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Gaya CSS Tambahan --}}
<style>
    .bg-light-custom {
        background-color: #f8f9fa !important;
    }
    .bg-gray-100-custom {
        background-color: #e9ecef !important;
    }
    .bg-primary-custom {
        background-color: #0d6efd !important; /* Warna primer Bootstrap */
    }
    .text-white-custom p, .text-white-custom td strong, .text-white-custom td { /* Pastikan semua teks putih */
        color: white !important;
    }
    .highlight-final-balance td {
         border-top: 2px solid #0a58ca !important; /* Border lebih gelap dari primary */
         border-bottom: 2px solid #0a58ca !important;
    }
    .text-xxs {
        font-size: 0.68rem !important;
    }
    .table-sm th, .table-sm td {
        padding-top: 0.4rem;
        padding-bottom: 0.4rem;
        vertical-align: middle;
    }
    .table-borderless th, .table-borderless td {
        border: 0;
    }
    .ps-4 { padding-left: 1.5rem !important; }
    .pe-3 { padding-right: 1rem !important; }
    .ms-3 { margin-left: 1rem !important; } /* Untuk indentasi item detail */
    .border-top-custom {
        border-top: 2px solid #dee2e6 !important;
    }
</style>
@endsection