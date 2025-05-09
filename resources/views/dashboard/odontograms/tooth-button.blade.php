@php
    // Ambil data odontogram untuk gigi ini
    $toothData = $odontograms[$toothNumber] ?? null;

    // Default class
    $toothClass = 'healthy';

    if ($toothData) {
        $conditionClassMap = [
            'healthy' => 'healthy',
            'cavity' => 'cavity',
            'filled' => 'filled',
            'temporary_filling' => 'temporary-filling',
            'extracted' => 'extracted',
            'extraction_needed' => 'extraction-needed',
            'missing' => 'missing',
            'impacted' => 'impacted',
            'root_remnants' => 'root-remnants',
            'fractured' => 'fractured',
            'sealant' => 'sealant',
            'un-erupted' => 'unerupted',
            'crown' => 'crown',
            'bridge' => 'bridge',
            'prosthesis' => 'prosthesis',
        ];

        $toothClass = $conditionClassMap[$toothData['condition']] ?? 'healthy';

        // Tambahkan class jika ada prosedur
        if (!empty($toothData['procedures'])) {
            $toothClass .= ' has-procedure';
        }
    }
@endphp

<div class="tooth-container {{ $toothClass }}" 
     data-tooth-number="{{ $toothNumber }}"
     data-bs-toggle="modal" 
     data-bs-target="#toothDetailModal">
    <div class="tooth-number">{{ $toothNumber }}</div>
    <div class="tooth-shape"></div>
</div>
