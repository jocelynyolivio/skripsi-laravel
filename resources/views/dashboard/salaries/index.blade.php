@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-4">
    <h2>Data Gaji Berdasarkan Absensi</h2>

    <!-- Form Filter -->
    <form method="GET" action="{{ route('dashboard.salaries.index') }}" class="mb-4">
        <div class="row">
            <!-- Filter Bulan -->
            <div class="col-md-4">
                <label for="month" class="form-label">Pilih Bulan:</label>
                <select name="month" id="month" class="form-select">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" 
                            {{ $month == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- Filter Tahun -->
            <div class="col-md-4">
                <label for="year" class="form-label">Pilih Tahun:</label>
                <select name="year" id="year" class="form-select">
                    @for ($y = now()->year - 5; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- Tombol Submit -->
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <!-- Tabel Data -->
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
            @foreach ($data as $row)
                <tr>
                    <td>{{ $row->no_id }}</td>
                    <td>{{ $row->nama }}</td>
                    <td>{{ $row->normal_shift }}</td>
                    <td>{{ $row->holiday_shift }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- DataTables -->
@section('scripts')
<script>
    $(document).ready(function() {
        $('#salariesTable').DataTable({
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
