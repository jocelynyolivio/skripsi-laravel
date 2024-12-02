@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Odontogram</h3>
    
    <form action="{{ route('odontogram.store', ['medicalRecordId' => $medicalRecordId]) }}" method="POST">
        @csrf
        <div id="odontogram-canvas" style="display: flex; justify-content: center; align-items: center;">
            <!-- SVG untuk Odontogram -->
            <svg id="odontogram-svg" width="600" height="400" xmlns="http://www.w3.org/2000/svg">
                <!-- Contoh gigi -->
                @foreach($odontogram as $tooth)
                    <rect x="{{ 50 * $loop->iteration }}" y="50" width="40" height="40" fill="{{ $tooth->status == 'sehat' ? 'green' : 'red' }}" data-tooth-number="{{ $tooth->tooth_number }}" />
                @endforeach
            </svg>
        </div>

        <input type="hidden" name="odontogram" id="odontogram-input" value="">

        <button type="submit" class="btn btn-primary mt-4">Save Odontogram</button>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const svg = document.getElementById('odontogram-svg');
        const input = document.getElementById('odontogram-input');

        // Simpan data odontogram ketika diklik
        svg.addEventListener('click', function(event) {
            if (event.target.tagName === 'rect') {
                const toothNumber = event.target.getAttribute('data-tooth-number');
                let status = event.target.getAttribute('fill') === 'green' ? 'berlubang' : 'sehat';
                event.target.setAttribute('fill', status === 'sehat' ? 'green' : 'red');

                let odontogramData = JSON.parse(input.value || '{}');
                odontogramData[toothNumber] = {
                    status: status,
                    notes: ''
                };
                input.value = JSON.stringify(odontogramData);
            }
        });
    });
</script>
@endsection
