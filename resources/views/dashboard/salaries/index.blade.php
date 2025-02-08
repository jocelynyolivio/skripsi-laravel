@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-4">
    <h2>Data Gaji Berdasarkan Absensi</h2>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Form Filter Bulan & Tahun -->
    <form method="GET" action="{{ route('dashboard.salaries.index') }}" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="month" class="form-label">Pilih Bulan:</label>
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
                <label for="year" class="form-label">Pilih Tahun:</label>
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

    <!-- Tabel Absensi -->
    <h3>Absensi Bulan {{ date('F Y', strtotime(request('year', now()->format('Y')) . '-' . request('month', now()->format('m')) . '-01')) }}</h3>
    <table id="salariesTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>No ID</th>
                <th>Nama</th>
                <th>Normal Shift</th>
                <th>Holiday Shift</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data ?? [] as $row)
                <tr>
                    <td>{{ $row->no_id }}</td>
                    <td>{{ $row->nama }}</td>
                    <td>{{ $row->normal_shift }}</td>
                    <td>{{ $row->holiday_shift }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tombol Hitung Gaji Admin -->
    <form method="POST" action="{{ route('dashboard.salaries.calculate') }}">
        @csrf
        <input type="hidden" name="month" value="{{ request('month', now()->format('m')) }}">
        <input type="hidden" name="year" value="{{ request('year', now()->format('Y')) }}">
        <button type="submit" class="btn btn-primary mt-3">Hitung Gaji Admin</button>
    </form>

    @if(isset($calculatedSalaries))
    <!-- Tabel Hasil Perhitungan Gaji Admin -->
    <h3 class="mt-5">Hasil Perhitungan Gaji Admin</h3>
    <table id="calculatedSalariesTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>No ID</th>
                <th>Nama</th>
                <th>Shift Pagi</th>
                <th>Shift Siang</th>
                <th>Holiday</th>
                <th>Lembur</th>
                <th>Gaji Pokok</th>
                <th>Tunjangan</th>
                <th>Grand Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($calculatedSalaries as $salary)
                <tr>
                    <td>{{ $salary['user_id'] }}</td>
                    <td>{{ $salary['nama'] }}</td>
                    <td>Rp. {{ number_format($salary['shift_pagi'], 2, ',', '.') }}</td>
                    <td>Rp. {{ number_format($salary['shift_siang'], 2, ',', '.') }}</td>
                    <td>Rp. {{ number_format($salary['holiday_shift'], 2, ',', '.') }}</td>
                    <td>Rp. {{ number_format($salary['lembur'], 2, ',', '.') }}</td>
                    <td>Rp. {{ number_format($salary['base_salary'], 2, ',', '.') }}</td>
                    <td>Rp. {{ number_format($salary['allowance'], 2, ',', '.') }}</td>
                    <td><b>Rp. {{ number_format($salary['grand_total'], 2, ',', '.') }}</b></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Tombol Hitung Gaji Dokter -->
    <form method="POST" action="{{ route('dashboard.salaries.doctor') }}">
        @csrf
        <input type="hidden" name="month" value="{{ request('month', now()->format('m')) }}">
        <input type="hidden" name="year" value="{{ request('year', now()->format('Y')) }}">
        <button type="submit" class="btn btn-primary mt-3">Hitung Gaji Dokter</button>
    </form>

    @if(isset($doctorSalaries))
    <!-- Tabel Hasil Perhitungan Gaji Dokter -->
    <h3 class="mt-5">Hasil Perhitungan Gaji Dokter</h3>
    <table id="doctorSalariesTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>No ID</th>
                <th>Nama</th>
                <th>Shift</th>
                <th>Total Transport</th>
                <th>Bagi Hasil</th>
                <th>Gaji Pokok</th>
                <th>Grand Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($doctorSalaries as $salary)
                <tr>
                    <td>{{ $salary['user_id'] }}</td>
                    <td>{{ $salary['nama'] }}</td>
                    <td>{{ $salary['shift_count'] }}</td>
                    <td>Rp. {{ number_format($salary['transport_total'], 2, ',', '.') }}</td>
                    <td>Rp. {{ number_format($salary['bagi_hasil'], 2, ',', '.') }}</td>
                    <td>Rp. {{ number_format($salary['base_salary'], 2, ',', '.') }}</td>
                    <td><b>Rp. {{ number_format($salary['grand_total'], 2, ',', '.') }}</b></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

@section('scripts')
<script>
    $(document).ready(function() {
        $('#salariesTable, #calculatedSalariesTable, #doctorSalariesTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true
        });
    });
</script>
@endsection
@endsection