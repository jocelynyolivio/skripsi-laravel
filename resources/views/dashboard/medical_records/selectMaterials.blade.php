@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Select Dental Materials for Medical Record</h3>

    <form method="POST" action="{{ route('dashboard.medical_records.saveMaterials', ['medicalRecordId' => $medicalRecordId]) }}">
    @csrf
    <div class="form-group">
        <label for="procedures">Procedures:</label>
        <ul>
            @foreach($procedures as $procedure)
                <li>{{ $procedure->name }}</li>
            @endforeach
        </ul>
    </div>

    <div class="form-group">
        <label for="materials">Dental Materials:</label>
        <table class="table">
            <thead>
                <tr>
                    <th>Material Name</th>
                    <th>Required Quantity</th>
                    <th>Available Stock</th>
                    <th>Selected Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($materials as $materialId => $material)
                    <tr>
                        <td>{{ $material['name'] }}</td>
                        <td>{{ $material['quantity'] }} (Required)</td>
                        <td>{{ $material['stock_quantity'] }}</td>
                        <td>
                            <!-- Menggunakan input dengan nilai default yang sudah diisi jumlah yang dibutuhkan -->
                            <input type="number" name="quantities[{{ $materialId }}]" 
                                   value="{{ $material['quantity'] }}" 
                                   min="0" max="{{ $material['stock_quantity'] }}">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <button type="submit" class="btn btn-primary">Save Materials</button>
</form>


</div>
@endsection
