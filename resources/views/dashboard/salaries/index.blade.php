@extends('dashboard.layouts.main')

@section('container')
<div class="container-fluid py-4">
<div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Attendances and Salaries</h3>
        <a href="{{ route('dashboard.salaries.upload') }}" class="btn btn-primary mb-3">Upload Attendances File</a>
    </div>

    <!-- Header and Filter Section -->
    <div class="card shadow-lg mb-4">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Attendances by Month</h4>
                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
            </div>
        </div>
        
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard.salaries.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="month" class="form-label">Month</label>
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
                        <label for="year" class="form-label">Year</label>
                        <select name="year" id="year" class="form-select">
                            @for ($y = now()->year - 5; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ request('year', now()->format('Y')) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i> Filter Data
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance Data Section -->
    <div class="card shadow-lg mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Attendances {{ date('F Y', strtotime(request('year', now()->format('Y')) . '-' . request('month', now()->format('m')) . '-01')) }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="salariesTable" class="table table-striped table-hover" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>No ID</th>
                            <th>Name</th>
                            <th class="text-center">Normal Shift</th>
                            <th class="text-center">Holiday Shift</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data ?? [] as $row)
                        <tr>
                            <td>{{ $row->no_id }}</td>
                            <td>{{ $row->nama }}</td>
                            <td class="text-center">{{ $row->normal_shift }}</td>
                            <td class="text-center">{{ $row->holiday_shift }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Admin Salary Calculation Section -->
    <div class="card shadow-lg mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Staffs' Salaries Calculation</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('dashboard.salaries.store') }}">
                @csrf
                <input type="hidden" name="month" value="{{ request('month', now()->format('m')) }}">
                <input type="hidden" name="year" value="{{ request('year', now()->format('Y')) }}">

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
                    <button type="submit" name="action" value="calculate" class="btn btn-primary">
                        <i class="fas fa-calculator me-2"></i>Generate Salaries for Staffs
                    </button>
                </div>

                @if(isset($calculatedSalaries))
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No ID</th>
                                <th>Nama</th>
                                <th class="text-end">Morning Shift</th>
                                <th class="text-end">Afternoon shift</th>
                                <th class="text-end">Holiday Shift</th>
                                <th class="text-end">Overtime</th>
                                <th class="text-end">Basic Salary</th>
                                <th class="text-end">Grand Total</th>
                                <th class="text-end">Adjustments (Optional)</th>
                                <th>Adjustment Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($calculatedSalaries as $index => $salary)
                            <tr>
                                <td>
                                    <input type="hidden" name="salaries[{{ $index }}][user_id]" value="{{ $salary['user_id'] }}">
                                    {{ $salary['user_id'] }}
                                </td>
                                <td>{{ $salary['nama'] }}</td>
                                <td class="text-end">
                                    Rp. {{ number_format($salary['shift_pagi'], 2, ',', '.') }}
                                    <input type="hidden" name="salaries[{{ $index }}][shift_pagi]" value="{{ $salary['shift_pagi'] }}">
                                </td>
                                <td class="text-end">
                                    Rp. {{ number_format($salary['shift_siang'], 2, ',', '.') }}
                                    <input type="hidden" name="salaries[{{ $index }}][shift_siang]" value="{{ $salary['shift_siang'] }}">
                                </td>
                                <td class="text-end">
                                    Rp. {{ number_format($salary['holiday_shift'], 2, ',', '.') }}
                                    <input type="hidden" name="salaries[{{ $index }}][holiday_shift]" value="{{ $salary['holiday_shift'] }}">
                                </td>
                                <td class="text-end">
                                    Rp. {{ number_format($salary['lembur'], 2, ',', '.') }}
                                    <input type="hidden" name="salaries[{{ $index }}][lembur]" value="{{ $salary['lembur'] }}">
                                </td>
                                <td class="text-end">
                                    Rp. {{ number_format($salary['base_salary'], 2, ',', '.') }}
                                    <input type="hidden" name="salaries[{{ $index }}][base_salary]" value="{{ $salary['base_salary'] }}">
                                </td>
                                <td class="text-end fw-bold">
                                    Rp. {{ number_format($salary['grand_total'], 2, ',', '.') }}
                                    <input type="hidden" name="salaries[{{ $index }}][grand_total]" value="{{ $salary['grand_total'] }}">
                                </td>
                                <td>
                                    <input type="number" name="salaries[{{ $index }}][adjustment]" class="form-control text-end" value="0">
                                </td>
                                <td>
                                    <textarea name="salaries[{{ $index }}][adjustment_notes]" class="form-control" rows="1"></textarea>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <label for="coa_out" class="form-label">Account for Salaries</label>
                        <select class="form-select" id="coa_out" name="coa_out" required>
                            <option value="">-- Choose Account --</option>
                            @foreach ($coa as $account)
                            <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end justify-content-end">
                        <button type="submit" name="action" value="store" class="btn btn-success">
                            <i class="fas fa-save me-2"></i> Save Salaries to Database
                        </button>
                    </div>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Doctor Salary Calculation Section -->
    <div class="card shadow-lg">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">Medical Personnels' Salaries Calculation</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('dashboard.salaries.doctor') }}">
                @csrf
                <input type="hidden" name="month" value="{{ request('month', now()->format('m')) }}">
                <input type="hidden" name="year" value="{{ request('year', now()->format('Y')) }}">
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-calculator me-2"></i>Generate Salaries for Medical Personnels
                    </button>
                </div>
            </form>

            @if(isset($doctorSalaries))
            <form method="POST" action="{{ route('dashboard.salaries.storeDoctor') }}">
                @csrf
                <input type="hidden" name="month" value="{{ request('month', now()->format('m')) }}">
                <input type="hidden" name="year" value="{{ request('year', now()->format('Y')) }}">

                <div class="table-responsive">
                    <table id="doctorSalariesTable" class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No ID</th>
                                <th>Nama</th>
                                <th class="text-center">Shift</th>
                                <th class="text-end">Accomodation</th>
                                <th class="text-end">Shared Revenue</th>
                                <th class="text-end">Basic Salary</th>
                                <th class="text-end">Grand Total</th>
                                <th class="text-end">Adjustment (Optional) </th>
                                <th>Adjustment Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($doctorSalaries as $index => $salary)
                            <tr>
                                <td>
                                    <input type="hidden" name="doctorSalaries[{{ $index }}][user_id]" value="{{ $salary['user_id'] }}">
                                    {{ $salary['user_id'] }}
                                </td>
                                <td>{{ $salary['nama'] }}</td>
                                <td class="text-center">
                                    {{ $salary['shift_count'] }}
                                    <input type="hidden" name="doctorSalaries[{{ $index }}][shift_count]" value="{{ $salary['shift_count'] }}">
                                </td>
                                <td class="text-end">
                                    Rp. {{ number_format($salary['transport_total'], 2, ',', '.') }}
                                    <input type="hidden" name="doctorSalaries[{{ $index }}][transport_total]" value="{{ $salary['transport_total'] }}">
                                </td>
                                <td class="text-end">
                                    Rp. {{ number_format($salary['bagi_hasil'], 2, ',', '.') }}
                                    <input type="hidden" name="doctorSalaries[{{ $index }}][bagi_hasil]" value="{{ $salary['bagi_hasil'] }}">
                                </td>
                                <td class="text-end">
                                    Rp. {{ number_format($salary['base_salary'], 2, ',', '.') }}
                                    <input type="hidden" name="doctorSalaries[{{ $index }}][base_salary]" value="{{ $salary['base_salary'] }}">
                                </td>
                                <td class="text-end fw-bold">
                                    Rp. {{ number_format($salary['grand_total'], 2, ',', '.') }}
                                    <input type="hidden" name="doctorSalaries[{{ $index }}][grand_total]" value="{{ $salary['grand_total'] }}">
                                </td>
                                <td>
                                    <input type="number" name="doctorSalaries[{{ $index }}][adjustment]" class="form-control text-end" value="0">
                                </td>
                                <td>
                                    <textarea name="doctorSalaries[{{ $index }}][adjustment_notes]" class="form-control" rows="1"></textarea>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <label for="coa_out" class="form-label">Account for Salaries</label>
                        <select class="form-select" id="coa_out" name="coa_out" required>
                            <option value="">-- Choose Account --</option>
                            @foreach ($coa as $account)
                            <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end justify-content-end">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i> Save Salaries to Database
                        </button>
                    </div>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTables
        $('#salariesTable, #doctorSalariesTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
            "dom": '<"top"f>rt<"bottom"lip><"clear">',
            "language": {
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak ada data tersedia",
                "infoFiltered": "(difilter dari _MAX_ total data)"
            }
        });

        // Format numeric inputs
        $('input[type="number"]').on('focus', function() {
            $(this).select();
        });
    });
</script>
@endsection