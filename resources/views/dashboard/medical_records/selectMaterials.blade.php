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
<div class="row justify-content-center">
    <div class="col-lg-10">

        @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif


        <div class="card mt-4">
            <div class="card-header">
                <h4 class="card-title mb-0">Select Dental Materials</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.medical_records.saveMaterials', ['medicalRecordId' => $medicalRecordId]) }}" id="saveMaterialsForm">
                    @csrf

                    {{-- Informasi Prosedur --}}
                    <div class="mb-4">
                        <p class="fw-bold">Procedures for Medical Record:</p>
                        <ul class="list-group">
                            @forelse($procedures as $procedure)
                            <li class="list-group-item">{{ $procedure->name }}</li>
                            @empty
                            <li class="list-group-item text-muted">No procedures selected.</li>
                            @endforelse
                        </ul>
                    </div>

                    {{-- Tabel Material yang Dibutuhkan --}}
                    <h5 class="mt-4 mb-3">Required Materials</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Material Name</th>
                                    <th>Required Quantity</th>
                                    <th>Unit</th>
                                    <th>Available Stock</th>
                                    <th style="width: 20%;">Selected Quantity</th>
                                </tr>
                            </thead>
                            <tbody id="materials-table-body">
                                @foreach($materials as $materialId => $material)
                                <tr>
                                    <td>{{ $material['name'] }}</td>
                                    <td class="text-center">{{ $material['quantity'] }}</td>
                                    <td class="text-center">{{ $material['unit_type'] }}</td>
                                    <td class="text-center">{{ $material['stock_quantity'] }}</td>
                                    <td>
                                        <input type="number" name="quantities[{{ $materialId }}]" min="0" max="{{ $material['stock_quantity'] }}" class="form-control text-center" placeholder="0" required>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Tabel Material Tambahan --}}
                    <h5 class="mt-4 mb-3">Additional Materials</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Material Name</th>
                                    <th>Unit</th>
                                    <th>Available Stock</th>
                                    <th style="width: 20%;">Selected Quantity</th>
                                </tr>
                            </thead>
                            <tbody id="extra-materials-table-body">
                                {{-- Baris baru akan ditambahkan oleh JavaScript di sini --}}
                            </tbody>
                        </table>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary" id="add-material-btn">
                            <i class="bi bi-plus-circle"></i> Add Material
                        </button>
                        <button type="button" class="btn btn-primary" id="submitSaveMaterials">
                            <i class="bi bi-save"></i> Save Materials
                        </button>
                    </div>

                    <template id="material-row-template">
                        <tr>
                            <td>
                                <select class="form-select material-select">
                                    <option value="" selected disabled>-- Select Material --</option>
                                    @foreach($allMaterials as $material)
                                    <option value="{{ $material->id }}" data-unit="{{ $material->unit_type }}" data-stock="{{ $material->stock_quantity }}">
                                        {{ $material->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <input type="hidden" class="material-id-hidden" name="">
                            </td>
                            <td class="unit-cell text-center">-</td>
                            <td class="stock-cell text-center">-</td>
                            <td>
                                <input type="number" class="form-control quantity-input text-center" name="" min="0" step="0.01" placeholder="0">
                            </td>
                        </tr>
                    </template>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let materialIndex = 0;

        document.getElementById('add-material-btn').addEventListener('click', function() {
            const template = document.getElementById('material-row-template');
            const clone = template.content.cloneNode(true);
            const row = clone.querySelector('tr');

            // Dapatkan elemen-elemen di dalam baris yang baru
            const select = row.querySelector('.material-select');
            const materialIdInput = row.querySelector('.material-id-hidden');
            const quantityInput = row.querySelector('.quantity-input');
            const unitCell = row.querySelector('.unit-cell');
            const stockCell = row.querySelector('.stock-cell');

            // Atur nama atribut dengan index dinamis untuk dikirim ke controller
            const newNamePrefix = `extra_materials[${materialIndex}]`;
            materialIdInput.name = `${newNamePrefix}[material_id]`;
            quantityInput.name = `${newNamePrefix}[selected_quantity]`;

            select.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const stock = selectedOption.getAttribute('data-stock');

                unitCell.innerText = selectedOption.getAttribute('data-unit') || '-';
                stockCell.innerText = stock || '-';
                materialIdInput.value = this.value;

                // Set max value untuk input quantity berdasarkan stok
                quantityInput.max = stock;
            });

            document.getElementById('extra-materials-table-body').appendChild(row);
            materialIndex++;
        });

        document.getElementById('submitSaveMaterials').addEventListener('click', function() {
            Swal.fire({
                title: 'Save Materials?',
                text: "Make sure all quantities are correct.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('saveMaterialsForm').submit();
                }
            });
        });
    });
</script>
@endsection