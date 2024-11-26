@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <!-- Title -->
        <h3>Salary Slip & Incentives</h3>

        <!-- Static Filter Section -->
        <div class="row w-50">
            <!-- Month Filter -->
            <div class="col-md-6">
                <label for="month" class="form-label">Select Month</label>
                <select class="form-select" id="month">
                    <option value="01">January</option>
                    <option value="02">February</option>
                    <option value="03">March</option>
                    <option value="04">April</option>
                    <option value="05">May</option>
                    <option value="06">June</option>
                    <option value="07">July</option>
                    <option value="08">August</option>
                    <option value="09">September</option>
                    <option value="10">October</option>
                    <option value="11" selected>November</option>
                    <option value="12">December</option>
                </select>
            </div>

            <!-- Year Filter -->
            <div class="col-md-6">
                <label for="year" class="form-label">Select Year</label>
                <select class="form-select" id="year">
                    <option value="2020">2020</option>
                    <option value="2021">2021</option>
                    <option value="2022">2022</option>
                    <option value="2023">2023</option>
                    <option value="2024" selected>2024</option>
                </select>
            </div>
        </div>
    </div>
</div>



    <!-- Display Salary Details -->
    <div class="card">
        <div class="card-body">
            <h6>Basic Salary</h6>
            <p>IDR 5,000,000</p>

            <h6>Incentives</h6>
            <p>Performance Incentive: IDR 1,000,000</p>
            <p>Bonus: IDR 500,000</p>

            <h6>Total Earnings</h6>
            <p>IDR 6,500,000</p>
        </div>
        <div class="card-footer text-muted text-end">
            <small>Generated on: {{ now()->format('d M Y') }}</small>
        </div>
    </div>
</div>
@endsection
