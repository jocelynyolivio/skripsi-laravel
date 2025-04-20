@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Procedure Materials']
        ]
    ])
@endsection
@section('container')
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Procedure Materials</h3>
        <a href="{{ route('dashboard.procedure_materials.create') }}" class="btn btn-primary mb-3">Create New Procedure Materials</a>
    </div>

    <div class="accordion" id="procedureAccordion">
        @foreach ($procedureMaterials->groupBy('procedure_id') as $procedureGroup)
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading-{{ $procedureGroup->first()->procedure->id }}">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $procedureGroup->first()->procedure->id }}" aria-expanded="true" aria-controls="collapse-{{ $procedureGroup->first()->procedure->id }}">
                        {{ $procedureGroup->first()->procedure->name }}
                    </button>
                </h2>
                <div id="collapse-{{ $procedureGroup->first()->procedure->id }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $procedureGroup->first()->procedure->id }}" data-bs-parent="#procedureAccordion">
                    <div class="accordion-body">
                        <table class="table table-striped" id="datatable-{{ $procedureGroup->first()->procedure->id }}">
                            <thead>
                                <tr>
                                    <th>Material Name</th>
                                    <th>Quantity</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($procedureGroup as $material)
                                    <tr>
                                        <td>{{ $material->dentalMaterial->name }}</td>
                                        <td>{{ $material->quantity }}</td>
                                        <td>
                                            <a href="{{ route('dashboard.procedure_materials.edit', $material->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                            <form action="{{ route('dashboard.procedure_materials.destroy', $material->id) }}" method="POST" style="display:inline;" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm delete-button">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    </div>    

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables untuk setiap tabel di dalam accordion
            $('table[id^="datatable-"]').each(function() {
                $(this).DataTable();
            });
        });

        // Event delegation for SweetAlert confirmation
        $('table[id^="datatable-"]').on('click', '.delete-button', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });                 
    </script>
@endsection