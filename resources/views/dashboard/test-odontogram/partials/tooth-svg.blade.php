@php
$ending = substr($toothNumber, -1);
$isLeftSide = in_array(substr($toothNumber, 0, 1), ['1', '5', '8', '4']);
$isUpper = in_array(substr($toothNumber, 0, 1), ['1', '2', '5', '6']);
$isPermanent = strlen($toothNumber) == 2 && substr($toothNumber, 0, 1) != '0';
@endphp

<div class="odontogram-card" data-tooth="{{ $toothNumber }}">
    <div class="tooth-number">{{ $toothNumber }}</div>

    @if(in_array($ending, ['4', '5', '6', '7', '8']))
    <!-- SVG untuk geraham dan premolar (5 bagian) -->
    <svg class="odontogram-svg" viewBox="0 0 200 200">
        <!-- Occlusal -->
        <polygon class="tooth-part" data-part="occlusal" points="80,80 120,80 120,120 80,120" fill="white" stroke="#333" stroke-width="1"/>
        
        @if($isLeftSide)
            <polygon class="tooth-part" data-part="distal" points="40,40 80,80 80,120 40,160" fill="white" stroke="#333" stroke-width="1"/>
            <polygon class="tooth-part" data-part="mesial" points="120,80 160,40 160,160 120,120" fill="white" stroke="#333" stroke-width="1"/>
        @else
            <polygon class="tooth-part" data-part="mesial" points="40,40 80,80 80,120 40,160" fill="white" stroke="#333" stroke-width="1"/>
            <polygon class="tooth-part" data-part="distal" points="120,80 160,40 160,160 120,120" fill="white" stroke="#333" stroke-width="1"/>
        @endif

        @php
        $frontPart = 'buccal';
        $backPart = $isUpper ? 'palatal' : 'lingual';

        // Gigi atas
        if ($isUpper) {
            if (in_array($toothNumber, ['13','12','11','21','22','23'])) {
                $frontPart = 'labial';
            } else {
                $frontPart = 'buccal';
            }
        } else {
            // Gigi bawah deciduous (anak-anak)
            if (in_array($toothNumber, ['85','84','74','75','48','47','46','45','44','34','35','36','37','38'])) {
                $frontPart = 'lingual';
                $backPart = 'buccal';
            }
        }
        @endphp

        <polygon class="tooth-part" data-part="{{ $frontPart }}" points="40,40 80,80 120,80 160,40" fill="white" stroke="#333" stroke-width="1"/>
        <polygon class="tooth-part" data-part="{{ $backPart }}" points="40,160 80,120 120,120 160,160" fill="white" stroke="#333" stroke-width="1"/>
    </svg>

    @elseif(in_array($ending, ['1', '2', '3']))
    <!-- SVG untuk insisivus dan kaninus (4 bagian) -->
    <svg class="odontogram-svg" viewBox="0 0 200 200">
        @if($isLeftSide)
            <polygon class="tooth-part" data-part="distal" points="40,40 80,90 80,90 40,160" fill="white" stroke="#333" stroke-width="1"/>
            <polygon class="tooth-part" data-part="mesial" points="120,90 160,40 160,160 120,90" fill="white" stroke="#333" stroke-width="1"/>
        @else
            <polygon class="tooth-part" data-part="mesial" points="40,40 80,90 80,90 40,160" fill="white" stroke="#333" stroke-width="1"/>
            <polygon class="tooth-part" data-part="distal" points="120,90 160,40 160,160 120,90" fill="white" stroke="#333" stroke-width="1"/>
        @endif

        @php
        $frontPart = 'labial';
        $backPart = $isUpper ? 'palatal' : 'lingual';

        if ($isUpper) {
            if (in_array($toothNumber, ['13','12','11','21','22','23'])) {
                $frontPart = 'labial';
            } elseif (in_array($toothNumber, ['55','54','64','65'])) {
                $frontPart = 'buccal';
            }
        } else {
           if (in_array($toothNumber, ['83','82','81','71','72','73','43','42','41','31','32','33'])) {
                $frontPart = 'lingual';
                $backPart = 'labial';
            }
        }
        @endphp

        <polygon class="tooth-part" data-part="{{ $frontPart }}" points="40,40 80,90 120,90 160,40" fill="white" stroke="#333" stroke-width="1"/>
        <polygon class="tooth-part" data-part="{{ $backPart }}" points="40,160 80,90 120,90 160,160" fill="white" stroke="#333" stroke-width="1"/>
    </svg>

    @else
    <div>?</div>
    @endif
</div>
