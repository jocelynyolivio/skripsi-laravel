@extends('dashboard.layouts.main')

@section('container')
    <h1>Procedure Materials</h1>

    <a href="{{ route('dashboard.procedure_materials.create') }}" class="btn btn-primary mb-3">Add New</a>

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
                        <ul class="list-group">
                            @foreach ($procedureGroup as $material)
                                <li class="list-group-item d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $material->dentalMaterial->name }}</strong> - Quantity: {{ $material->quantity }}
                                    </div>
                                    <div>
                                        <a href="{{ route('dashboard.procedure_materials.edit', $material->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('dashboard.procedure_materials.destroy', $material->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
