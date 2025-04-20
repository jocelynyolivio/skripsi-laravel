@extends('dashboard.layouts.main')

@section('container')
<div class="container">
    <h1>Daftar Gaji</h1>
    <a href="{{ route('dashboard.salary_calculations.create') }}" class="btn btn-primary mb-3">Create Salaries</a>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Bulan</th>
                <th>Normal Shift</th>
                <th>Holiday Shift</th>
                <th>Shift Pagi</th>
                <th>Shift Siang</th>
                <th>Lembur</th>
                <th>Gaji Pokok</th>
                <th>Tunjangan</th>
                <th>Total Gaji</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($salaries as $salary)
                <tr>
                    <td>{{ $salary->id }}</td>
                    <td>{{ $salary->user_id }}</td>
                    <td>{{ $salary->month }}</td>
                    <td>{{ $salary->normal_shift }}</td>
                    <td>{{ $salary->holiday_shift }}</td>
                    <td>{{ $salary->shift_pagi }}</td>
                    <td>{{ $salary->shift_siang }}</td>
                    <td>{{ $salary->lembur }}</td>
                    <td>{{ $salary->base_salary }}</td>
                    <td>{{ $salary->allowance }}</td>
                    <td>{{ $salary->grand_total }}</td>
                    <td>
                        <a href="{{ route('salary_calculations.edit', $salary->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('salary_calculations.destroy', $salary->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are You Sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
