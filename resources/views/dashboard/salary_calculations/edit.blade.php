@extends('dashboard.layouts.main')

@section('container')
<div class="container">
    <h1>Edit Data Gaji</h1>
    <form action="{{ route('dashboard.salary_calculations.update', $salary->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>User ID</label>
            <input type="number" name="user_id" class="form-control" value="{{ $salary->user_id }}" required>
        </div>

        <div class="form-group">
            <label>Bulan</label>
            <input type="month" name="month" class="form-control" value="{{ $salary->month }}" required>
        </div>

        <div class="form-group">
            <label>Normal Shift</label>
            <input type="number" name="normal_shift" class="form-control" value="{{ $salary->normal_shift }}">
        </div>

        <div class="form-group">
            <label>Holiday Shift</label>
            <input type="number" name="holiday_shift" class="form-control" value="{{ $salary->holiday_shift }}">
        </div>

        <div class="form-group">
            <label>Shift Pagi</label>
            <input type="number" name="shift_pagi" class="form-control" value="{{ $salary->shift_pagi }}">
        </div>

        <div class="form-group">
            <label>Shift Siang</label>
            <input type="number" name="shift_siang" class="form-control" value="{{ $salary->shift_siang }}">
        </div>

        <div class="form-group">
            <label>Lembur</label>
            <input type="number" name="lembur" class="form-control" value="{{ $salary->lembur }}">
        </div>

        <div class="form-group">
            <label>Gaji Pokok</label>
            <input type="number" name="base_salary" class="form-control" value="{{ $salary->base_salary }}" required>
        </div>

        <div class="form-group">
            <label>Tunjangan</label>
            <input type="number" name="allowance" class="form-control" value="{{ $salary->allowance }}">
        </div>

        <div class="form-group">
            <label>Total Gaji</label>
            <input type="number" name="grand_total" class="form-control" value="{{ $salary->grand_total }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
