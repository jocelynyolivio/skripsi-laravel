@extends('dashboard.layouts.main')

@section('container')
<div class="container col-md-6">
    <h1>Add Salaries</h1>
    <form action="{{ route('dashboard.salary_calculations.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label>User ID</label>
            <input type="number" name="user_id" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Bulan</label>
            <input type="month" name="month" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Normal Shift</label>
            <input type="number" name="normal_shift" class="form-control">
        </div>

        <div class="form-group">
            <label>Holiday Shift</label>
            <input type="number" name="holiday_shift" class="form-control">
        </div>

        <div class="form-group">
            <label>Shift Pagi</label>
            <input type="number" name="shift_pagi" class="form-control">
        </div>

        <div class="form-group">
            <label>Shift Siang</label>
            <input type="number" name="shift_siang" class="form-control">
        </div>

        <div class="form-group">
            <label>Lembur</label>
            <input type="number" name="lembur" class="form-control">
        </div>

        <div class="form-group">
            <label>Gaji Pokok</label>
            <input type="number" name="base_salary" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Tunjangan</label>
            <input type="number" name="allowance" class="form-control">
        </div>

        <div class="form-group">
            <label>Total Gaji</label>
            <input type="number" name="grand_total" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
