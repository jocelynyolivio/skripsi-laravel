@php
    $toothProcedures = $procedureOdontograms->get($toothNumber, collect());
    $hasMedicalRecord = $toothProcedures->isNotEmpty();
    $toothManual = $odontograms->get($toothNumber);
    $manualCondition = $toothManual->condition ?? null;

    $toothClass = '';
    if ($hasMedicalRecord) {
        $toothClass = 'has-procedure';
    } elseif ($manualCondition) {
        $toothClass = match($manualCondition) {
            'cavity' => 'cavity',
            'filled' => 'filled',
            'extracted' => 'extracted',
            default => 'healthy',
        };
    } else {
        $toothClass = 'healthy';
    }
@endphp

<div class="tooth-container {{ $toothClass }}" 
     data-tooth-number="{{ $toothNumber }}"
     data-bs-toggle="modal" 
     data-bs-target="#toothDetailModal">
    <div class="tooth-number">{{ $toothNumber }}</div>
    <div class="tooth-shape"></div>
</div>