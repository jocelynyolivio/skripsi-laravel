@extends('dashboard.layouts.main') {{-- Pastikan layout utama Anda --}}

@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Balance Sheet']
        ]
    ])
@endsection

@section('container')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Laporan Neraca</h4>
                {{-- Form untuk filter tanggal dan periode --}}
                <form action="{{ route('dashboard.reports.balance_sheet') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-auto">
                        <label for="period" class="visually-hidden">Periode</label>
                        <select name="period" id="period" class="form-select form-select-sm">
                            {{-- Menggunakan variabel $periodForDisplay dari controller --}}
                            <option value="monthly" {{ ($periodForDisplay ?? 'monthly') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                            <option value="yearly" {{ ($periodForDisplay ?? 'monthly') == 'yearly' ? 'selected' : '' }}>Tahunan</option>
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
                {{-- Menampilkan tanggal laporan yang diformat --}}
                <p class="mb-0 text-sm">Per {{ \Carbon\Carbon::parse($dateInput ?? \Carbon\Carbon::now())->isoFormat('D MMMM YYYY') }}</p>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                {{-- Kolom Kiri: Aset --}}
                <div class="col-md-6">
                    <div class="table-responsive">
                        <table class="table table-sm align-items-center mb-4">
                            <thead class="bg-light-custom">
                                <tr>
                                    <th colspan="2" class="text-uppercase text-secondary text-xxs font-weight-bolder ps-4">Aset</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assets as $coa)
                                <tr>
                                    <td class="ps-4">
                                        {{-- Menampilkan nama akun dengan indentasi jika merupakan sub-akun (opsional, tergantung struktur CoA) --}}
                                        <p class="text-xs font-weight-normal mb-0 @if(substr_count($coa->coa_code, '-') > 1) ps-3 @endif">
                                            {{ $coa->coa_name }}
                                        </p>
                                    </td>
                                    <td class="text-end pe-3">
                                        {{-- Menampilkan saldo akun aset. Controller sudah menghitung $coa->balance --}}
                                        <p class="text-xs font-weight-normal mb-0">{{ number_format($coa->balance, 2, ',', '.') }}</p>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-xs ps-4">Tidak ada data aset.</td>
                                </tr>
                                @endforelse
                                <tr class="bg-gray-100-custom">
                                    <td class="ps-4">
                                        <p class="text-xs font-weight-bold mb-0">TOTAL ASET</p>
                                    </td>
                                    <td class="text-end pe-3">
                                        <p class="text-xs font-weight-bold mb-0">{{ number_format($totalAssets, 2, ',', '.') }}</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Kolom Kanan: Liabilitas dan Ekuitas --}}
                <div class="col-md-6">
                    <div class="table-responsive">
                        <table class="table table-sm align-items-center mb-4">
                            <thead class="bg-light-custom">
                                <tr>
                                    <th colspan="2" class="text-uppercase text-secondary text-xxs font-weight-bolder ps-4">Liabilitas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($liabilities as $coa)
                                <tr>
                                    <td class="ps-4">
                                        <p class="text-xs font-weight-normal mb-0 @if(substr_count($coa->coa_code, '-') > 1) ps-3 @endif">
                                            {{ $coa->coa_name }}
                                        </p>
                                    </td>
                                    <td class="text-end pe-3">
                                        {{-- Menampilkan saldo akun liabilitas. Controller sudah menghitung $coa->balance --}}
                                        <p class="text-xs font-weight-normal mb-0">{{ number_format($coa->balance, 2, ',', '.') }}</p>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-xs ps-4">Tidak ada data liabilitas.</td>
                                </tr>
                                @endforelse
                                <tr class="bg-gray-100-custom">
                                    <td class="ps-4">
                                        <p class="text-xs font-weight-bold mb-0">TOTAL LIABILITAS</p>
                                    </td>
                                    <td class="text-end pe-3">
                                        <p class="text-xs font-weight-bold mb-0">{{ number_format($totalLiabilities, 2, ',', '.') }}</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm align-items-center mb-0">
                            <thead class="bg-light-custom">
                                <tr>
                                    <th colspan="2" class="text-uppercase text-secondary text-xxs font-weight-bolder ps-4">Ekuitas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($equities as $coa)
                                <tr>
                                    <td class="ps-4">
                                        <p class="text-xs font-weight-normal mb-0 @if(substr_count($coa->coa_code, '-') > 1) ps-3 @endif">
                                            {{ $coa->coa_name }}
                                        </p>
                                    </td>
                                    <td class="text-end pe-3">
                                        {{-- Menampilkan saldo akun ekuitas. Controller sudah menghitung $coa->balance --}}
                                        <p class="text-xs font-weight-normal mb-0">{{ number_format($coa->balance, 2, ',', '.') }}</p>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-xs ps-4">Tidak ada data ekuitas.</td>
                                </tr>
                                @endforelse
                                <tr class="bg-gray-100-custom">
                                    <td class="ps-4">
                                        <p class="text-xs font-weight-bold mb-0">TOTAL EKUITAS</p>
                                    </td>
                                    <td class="text-end pe-3">
                                        <p class="text-xs font-weight-bold mb-0">{{ number_format($totalEquities, 2, ',', '.') }}</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table table-sm align-items-center">
                            <tbody>
                                <tr class="bg-gray-200-custom">
                                    <td class="ps-4">
                                        <p class="text-sm font-weight-bolder mb-0">TOTAL LIABILITAS & EKUITAS</p>
                                    </td>
                                    <td class="text-end pe-3">
                                        <p class="text-sm font-weight-bolder mb-0">{{ number_format($totalLiabilities + $totalEquities, 2, ',', '.') }}</p>
                                    </td>
                                </tr>
                                {{-- Opsional: Tampilkan Selisih (Harusnya Nol) untuk verifikasi --}}
                                <tr class="bg-light-custom">
                                    <td class="ps-4">
                                        <p class="text-xs font-weight-bold mb-0">ASET - (LIABILITAS + EKUITAS)</p>
                                    </td>
                                    <td class="text-end pe-3">
                                        <p class="text-xs font-weight-bold mb-0 {{ abs($totalAssets - ($totalLiabilities + $totalEquities)) > 0.01 ? 'text-danger' : 'text-success' }}">
                                            {{ number_format($totalAssets - ($totalLiabilities + $totalEquities), 2, ',', '.') }}
                                        </p>
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

{{-- Gaya CSS tambahan, bisa dipindah ke file CSS terpisah jika diinginkan --}}
<style>
    .bg-light-custom { /* Mengganti nama agar tidak konflik dengan Bootstrap jika ada .bg-light */
        background-color: #f8f9fa !important; /* Warna terang standar Bootstrap */
    }
    .bg-gray-100-custom {
        background-color: #e9ecef !important; /* Sedikit lebih gelap dari bg-light */
    }
    .bg-gray-200-custom {
        background-color: #dee2e6 !important; /* Lebih gelap lagi */
    }
    .text-xxs {
        font-size: 0.68rem !important; /* Sedikit disesuaikan agar tidak terlalu kecil */
    }
    .table-sm th, .table-sm td {
        padding-top: 0.4rem;
        padding-bottom: 0.4rem;
    }
    /* Menambahkan padding kanan untuk sel nilai agar tidak terlalu mepet */
    .pe-3 {
        padding-right: 1rem !important;
    }
</style>
@endsection