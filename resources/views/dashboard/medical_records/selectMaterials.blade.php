@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Master Patients', 'url' => route('dashboard.masters.patients')],
            ['text' => 'Medical Record for ' . $patientName, 'url' => route('dashboard.medical_records.index', $patientId)],
            ['text' => 'Select Materials']
        ]
    ])
@endsection


@section('container')
<div class="container mt-5">
    <h3 class="text-center">Dental Materials for Medical Record a</h3>

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
        <form method="POST" action="{{ route('dashboard.medical_records.saveMaterials', ['medicalRecordId' => $medicalRecordId]) }}">
            @csrf
            <table class="table">
                <thead>
                    <tr>
                        <th>Material Name</th>
                        <th>Required Quantity</th>
                        <th>Unit</th>
                        <th>Available Stock</th>
                        <th>Selected Quantity</th>
                    </tr>
                </thead>
                <tr id="material-row-template" class="d-none">
                    <td>
                        <select name="extra_materials[][material_id]" class="form-control material-select">
                            <option value="">-- Select Material --</option>
                            @foreach($allMaterials as $material)
                            <option value="{{ $material->id }}"
                                data-unit="{{ $material->unit_type }}"
                                data-stock="{{ $material->stock_quantity }}">
                                {{ $material->name }}
                            </option>
                            @endforeach
                        </select>
                    </td>
                    <td>-</td> <!-- Required Quantity kosong -->
                    <td class="unit-cell"></td>
                    <td class="stock-cell"></td>
                    <td>
                        <input type="number" name="extra_materials[][selected_quantity]" class="form-control" min="0" step="0.01">
                    </td>
                </tr>

                <tbody>
                    @foreach($materials as $materialId => $material)
                    <tr>
                        <td>{{ $material['name'] }}</td>
                        <td>{{ $material['quantity'] }} (Required)</td>
                        <td>{{ $material['unit_type'] }}</td>
                        <td>{{ $material['stock_quantity'] }}</td>
                        <td>
                            <input type="number" name="quantities[{{ $materialId }}]"
                                min="0" max="{{ $material['stock_quantity'] }}">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="button" class="btn btn-secondary" id="add-material-btn">+ Add Material</button>

            <button type="submit" class="btn btn-primary">Save Materials</button>
        </form>
    </div>
</div>
<script>
document.getElementById('add-material-btn').addEventListener('click', function () {
    const template = document.getElementById('material-row-template');
    const clone = template.cloneNode(true);
    clone.classList.remove('d-none');
    clone.removeAttribute('id');

    // Tambahkan event listener untuk select dalam clone
    clone.querySelector('.material-select').addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const unit = selected.getAttribute('data-unit');
        const stock = selected.getAttribute('data-stock');

        // Tampilkan data unit dan stock di kolom
        const row = this.closest('tr');
        row.querySelector('.unit-cell').innerText = unit || '-';
        row.querySelector('.stock-cell').innerText = stock || '-';
    });

    document.querySelector('table tbody').appendChild(clone);
});
</script>


@endsection