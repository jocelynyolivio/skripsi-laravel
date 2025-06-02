@extends('dashboard.layouts.main') {{-- Pastikan layout utama Anda --}}

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
                {{-- Form untuk filter periode dan tanggal --}}
                <form action="{{ route('dashboard.reports.income_statement') }}" method="GET" class="row g-2 align-items-center">
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
            {{-- Bagian Pendapatan --}}
            <div class="table-responsive">
                <table class="table table-sm align-items-center mb-0">
                    <thead class="bg-light-custom">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-4">Pendapatan (Kotor)</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-end pe-3">Jumlah (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grossRevenuesData as $item)
                        <tr>
                            <td class="ps-4">
                                <p class="text-xs font-weight-normal mb-0">{{ $item->code }} - {{ $item->name }}</p>
                            </td>
                            <td class="text-end pe-3">
                                <p class="text-xs font-weight-normal mb-0">{{ number_format($item->amount, 2, ',', '.') }}</p>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-xs ps-4">Tidak ada data pendapatan kotor.</td>
                        </tr>
                        @endforelse
                        <tr class="bg-gray-100-custom">
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">Total Pendapatan (Kotor)</p>
                            </td>
                            <td class="text-end pe-3">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($totalGrossRevenue, 2, ',', '.') }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Bagian Kontra Pendapatan (Jika Ada) --}}
            @if(isset($contraRevenuesData) && $contraRevenuesData->count() > 0)
            <div class="table-responsive mt-3">
                <table class="table table-sm align-items-center mb-0">
                    <thead class="bg-light-custom">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-4">Pengurang Pendapatan</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-end pe-3">Jumlah (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contraRevenuesData as $item)
                        <tr>
                            <td class="ps-4">
                                <p class="text-xs font-weight-normal mb-0 ms-3">{{ $item->code }} - {{ $item->name }}</p>
                            </td>
                            <td class="text-end pe-3">
                                {{-- Ditampilkan dalam kurung sebagai pengurang --}}
                                <p class="text-xs font-weight-normal mb-0">({{ number_format($item->amount, 2, ',', '.') }})</p>
                            </td>
                        </tr>
                        @endforeach
                        <tr class="bg-gray-100-custom">
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">Total Pengurang Pendapatan</p>
                            </td>
                            <td class="text-end pe-3">
                                <p class="text-xs font-weight-bold mb-0">({{ number_format($totalContraRevenue, 2, ',', '.') }})</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endif

            {{-- Pendapatan Bersih --}}
            <div class="table-responsive {{ (isset($contraRevenuesData) && $contraRevenuesData->count() > 0) ? 'mt-1' : 'mt-3' }}">
                <table class="table table-sm align-items-center mb-0">
                    <tbody>
                        <tr class="highlight-row-custom">
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">Pendapatan Bersih</p>
                            </td>
                            <td class="text-end pe-3">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($totalNetRevenue, 2, ',', '.') }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Bagian Beban Pokok Pendapatan (HPP) --}}
            <div class="table-responsive mt-4">
                <table class="table table-sm align-items-center mb-0">
                    <thead class="bg-light-custom">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-4">Beban Pokok Pendapatan (HPP)</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-end pe-3">Jumlah (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hppData as $item)
                        <tr>
                            <td class="ps-4">
                                <p class="text-xs font-weight-normal mb-0">{{ $item->code }} - {{ $item->name }}</p>
                            </td>
                            <td class="text-end pe-3">
                                <p class="text-xs font-weight-normal mb-0">{{ number_format($item->amount, 2, ',', '.') }}</p>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-xs ps-4">Tidak ada data HPP.</td>
                        </tr>
                        @endforelse
                        <tr class="bg-gray-100-custom">
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">Total Beban Pokok Pendapatan</p>
                            </td>
                            <td class="text-end pe-3">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($totalHpp, 2, ',', '.') }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Laba Kotor --}}
            <div class="table-responsive mt-1">
                <table class="table table-sm align-items-center mb-0">
                    <tbody>
                        <tr class="highlight-row-custom border-top-custom">
                            <td class="ps-4">
                                <p class="text-xs font-weight-bolder mb-0">LABA KOTOR</p>
                            </td>
                            <td class="text-end pe-3">
                                <p class="text-xs font-weight-bolder mb-0">{{ number_format($grossProfit, 2, ',', '.') }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Bagian Beban Operasional (Kotor) --}}
            <div class="table-responsive mt-4">
                <table class="table table-sm align-items-center mb-0">
                    <thead class="bg-light-custom">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-4">Beban Operasional (Kotor)</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-end pe-3">Jumlah (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($operatingExpensesData as $item)
                        <tr>
                            <td class="ps-4">
                                <p class="text-xs font-weight-normal mb-0">{{ $item->code }} - {{ $item->name }}</p>
                            </td>
                            <td class="text-end pe-3">
                                <p class="text-xs font-weight-normal mb-0">{{ number_format($item->amount, 2, ',', '.') }}</p>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-xs ps-4">Tidak ada data beban operasional kotor.</td>
                        </tr>
                        @endforelse
                        <tr class="bg-gray-100-custom">
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">Total Beban Operasional (Kotor)</p>
                            </td>
                            <td class="text-end pe-3">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($totalGrossOperatingExpenses, 2, ',', '.') }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Bagian Kontra Beban (Jika Ada) --}}
            @if(isset($contraExpensesData) && $contraExpensesData->count() > 0)
            <div class="table-responsive mt-3">
                <table class="table table-sm align-items-center mb-0">
                    <thead class="bg-light-custom">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-4">Pengurang Beban Operasional</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-end pe-3">Jumlah (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contraExpensesData as $item)
                        <tr>
                            <td class="ps-4">
                                <p class="text-xs font-weight-normal mb-0 ms-3">{{ $item->code }} - {{ $item->name }}</p>
                            </td>
                            <td class="text-end pe-3">
                                {{-- Ditampilkan dalam kurung sebagai pengurang --}}
                                <p class="text-xs font-weight-normal mb-0">({{ number_format($item->amount, 2, ',', '.') }})</p>
                            </td>
                        </tr>
                        @endforeach
                        <tr class="bg-gray-100-custom">
                            <td class="ps-4">
                                <p class="text-xs font-weight-bold mb-0">Total Pengurang Beban Operasional</p>
                            </td>
                            <td class="text-end pe-3">
                                <p class="text-xs font-weight-bold mb-0">({{ number_format($totalContraExpense, 2, ',', '.') }})</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endif

            {{-- Laba Bersih --}}
            <div class="table-responsive mt-4">
                <table class="table table-sm align-items-center">
                    <tbody>
                        <tr class="bg-primary-custom text-white-custom">
                            <td class="ps-4">
                                <p class="text-sm font-weight-bolder mb-0">LABA BERSIH</p>
                            </td>
                            <td class="text-end pe-3">
                                <p class="text-sm font-weight-bolder mb-0">{{ number_format($netIncome, 2, ',', '.') }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Gaya CSS tambahan (sama seperti di view Neraca, bisa dipindah ke file CSS terpisah) --}}
<style>
    .bg-light-custom {
        background-color: #f8f9fa !important;
    }
    .bg-gray-100-custom {
        background-color: #e9ecef !important;
    }
    .highlight-row-custom {
        background-color: #ddeeff !important; /* Warna highlight untuk subtotal penting */
        font-weight: bold;
    }
    .highlight-row-custom p {
        font-weight: bold !important;
    }
    .border-top-custom {
        border-top: 2px solid #cfe2ff !important; /* Garis pemisah yang lebih jelas */
    }
    .bg-primary-custom {
        background-color: #0d6efd !important; /* Warna primer Bootstrap */
    }
    .text-white-custom p {
        color: white !important;
    }
    .text-xxs {
        font-size: 0.68rem !important;
    }
    .table-sm th, .table-sm td {
        padding-top: 0.4rem;
        padding-bottom: 0.4rem;
        vertical-align: middle;
    }
    .ps-4 {
        padding-left: 1.5rem !important;
    }
    .pe-3 {
        padding-right: 1rem !important;
    }
    .ms-3 {
        margin-left: 1rem !important;
    }
</style>
@endsection