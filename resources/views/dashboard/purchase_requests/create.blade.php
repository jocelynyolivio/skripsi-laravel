@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-8">
    <h3 class="text-center">Create Purchase Request</h3>
    <form action="{{ route('dashboard.purchase_requests.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Request Date:</label>
            <input type="date" name="request_date" class="form-control" value="{{ old('request_date', now()->format('Y-m-d')) }}" required>
        </div>

        <!-- Purchase Request Details -->
        <h5>Dental Materials</h5>
        <table class="table table-bordered" id="materialsTable">
            <thead>
                <tr>
                    <th>Material</th>
                    <th>Quantity</th>
                    <th>Notes</th>
                    <th>
                        <button type="button" class="btn btn-sm btn-success" id="addRow">+</button>
                    </th>
                </tr>
            </thead>
            <tbody>
                @php
                    $materialsInput = old('materials', $duplicateFrom->details ?? [null]);
                @endphp

                @foreach ($materialsInput as $i => $detail)
                <tr>
                    <td>
                        <select name="materials[{{ $i }}][dental_material_id]" class="form-control" required>
                            <option value="" disabled {{ !isset($detail) ? 'selected' : '' }}>Choose Material</option>
                            @foreach($materials as $material)
                            <option value="{{ $material->id }}"
                                {{ (old("materials.$i.dental_material_id", $detail->dental_material_id ?? '') == $material->id) ? 'selected' : '' }}>
                                {{ $material->name }}
                            </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="materials[{{ $i }}][quantity]" class="form-control" min="1"
                            value="{{ old("materials.$i.quantity", $detail->quantity ?? '') }}" required>
                    </td>
                    <td>
                        <input type="text" name="materials[{{ $i }}][notes]" class="form-control"
                            value="{{ old("materials.$i.notes", $detail->notes ?? '') }}">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm removeRow">-</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mb-3">
            <label class="form-label">General Notes:</label>
            <textarea name="notes" class="form-control">{{ old('notes', $duplicateFrom->notes ?? '') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100">Submit Request</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let rowIndex = {{ count($materialsInput) }};

        document.getElementById('addRow').addEventListener('click', function () {
            const tableBody = document.querySelector('#materialsTable tbody');
            const newRow = document.createElement('tr');

            newRow.innerHTML = `
                <td>
                    <select name="materials[${rowIndex}][dental_material_id]" class="form-control" required>
                        <option value="" disabled selected>Choose Material</option>
                        @foreach($materials as $material)
                        <option value="{{ $material->id }}">{{ $material->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="materials[${rowIndex}][quantity]" class="form-control" min="1" required>
                </td>
                <td>
                    <input type="text" name="materials[${rowIndex}][notes]" class="form-control">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm removeRow">-</button>
                </td>
            `;

            tableBody.appendChild(newRow);
            rowIndex++;
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('removeRow')) {
                const row = e.target.closest('tr');
                const rows = document.querySelectorAll('#materialsTable tbody tr');
                if (rows.length > 1) row.remove();
            }
        });
    });
</script>
@endsection
