@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Master Patients', 'url' => route('dashboard.masters.patients')],
            ['text' => 'Kondisi Gigi untuk '. $patient->name]
        ]
    ])
@endsection
@section('container')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0 text-center">KONDISI GIGI - {{ $patient->name }}</h4>
        </div>
        
        <div class="card-body">
            <!-- Odontogram Visual Layout -->
            <div class="odontogram-wrapper">
                <!-- Upper Jaw -->
                <div class="jaw upper-jaw d-flex justify-content-center">
                    <!-- Right Upper (18-11) -->
                    <div class="quadrant me-4">
                        @foreach([18,17,16,15,14,13,12,11] as $toothNumber)
                            @include('dashboard.odontograms.tooth-button', ['toothNumber' => $toothNumber])
                        @endforeach
                    </div>
                    
                    <!-- Left Upper (21-28) -->
                    <div class="quadrant">
                        @foreach([21,22,23,24,25,26,27,28] as $toothNumber)
                            @include('dashboard.odontograms.tooth-button', ['toothNumber' => $toothNumber])
                        @endforeach
                    </div>
                </div>
                
                <!-- Lower Jaw -->
                <div class="jaw lower-jaw d-flex justify-content-center">
                    <!-- Left Lower (38-31) -->
                    <div class="quadrant me-4">
                        @foreach([38,37,36,35,34,33,32,31] as $toothNumber)
                            @include('dashboard.odontograms.tooth-button', ['toothNumber' => $toothNumber])
                        @endforeach
                    </div>
                    
                    <!-- Right Lower (48-41) -->
                    <div class="quadrant">
                        @foreach([48,47,46,45,44,43,42,41] as $toothNumber)
                            @include('dashboard.odontograms.tooth-button', ['toothNumber' => $toothNumber])
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Tooth Symbols Legend -->
            <div class="legend-container mt-4 p-3 border rounded">
                <h5 class="text-center mb-3">SIMBOL DAN KETERANGAN KONDISI GIGI</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-2">
                            <div class="symbol healthy me-2"></div>
                            <span>Gigi Sehat</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="symbol cavity me-2"></div>
                            <span>Karies (Lubang)</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-2">
                            <div class="symbol filled me-2"></div>
                            <span>Tambalan</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="symbol extracted me-2"></div>
                            <span>Gigi Dicabut</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tooth Detail Modal -->
<div class="modal fade" id="toothDetailModal" tabindex="-1" aria-labelledby="toothDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="toothDetailModalLabel">Detail Gigi #<span id="modalToothNumber"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="toothForm" action="{{ route('dashboard.odontograms.store', ['patientId' => $patient->id]) }}" method="POST">
                @csrf
                <input type="hidden" name="tooth_number" id="formToothNumber">
                
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Kondisi Gigi</label>
                            <select name="condition" class="form-select" id="toothCondition">
                                <option value="healthy">Sehat</option>
                                <option value="cavity">Karies</option>
                                <option value="filled">Tambalan</option>
                                <option value="extracted">Dicabut</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Prosedur</label>
                            <select name="procedure_id[]" class="form-select" multiple id="toothProcedures">
                                @foreach($procedures as $procedure)
                                    <option value="{{ $procedure->id }}">{{ $procedure->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" id="toothNotes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .odontogram-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .jaw {
        margin: 20px 0;
    }
    
    .upper-jaw {
        border-bottom: 2px solid #333;
        padding-bottom: 30px;
    }
    
    .lower-jaw {
        border-top: 2px solid #333;
        padding-top: 30px;
    }
    
    .quadrant {
        display: flex;
        gap: 5px;
    }
    
    .tooth-container {
        width: 50px;
        height: 70px;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
    }
    
    .tooth-number {
        font-size: 10px;
        color: #333;
        margin-bottom: 2px;
    }
    
    .tooth-shape {
        width: 40px;
        height: 50px;
        border: 1px solid #333;
        border-radius: 5px;
        position: relative;
        overflow: hidden;
    }
    
    /* Condition Styles */
    .healthy .tooth-shape {
        background-color: #f8f9fa;
    }
    
    .cavity .tooth-shape {
        background: repeating-linear-gradient(
            45deg,
            #f8f9fa,
            #f8f9fa 5px,
            #ffc107 5px,
            #ffc107 10px
        );
    }
    
    .filled .tooth-shape {
        background-color: #0dcaf0;
    }
    
    .extracted .tooth-shape {
        background-color: #6c757d;
    }
    
    .extracted .tooth-shape:after {
        content: "×";
        position: absolute;
        font-size: 30px;
        color: white;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    
    .has-procedure .tooth-shape {
        background-color: #198754;
    }
    
    /* Legend Styles */
    .symbol {
        width: 25px;
        height: 30px;
        border: 1px solid #333;
        border-radius: 3px;
    }
    
    .symbol.healthy { background-color: #f8f9fa; }
    .symbol.cavity { background: repeating-linear-gradient(45deg, #f8f9fa, #f8f9fa 3px, #ffc107 3px, #ffc107 6px); }
    .symbol.filled { background-color: #0dcaf0; }
    .symbol.extracted { background-color: #6c757d; position: relative; }
    .symbol.extracted:after {
        content: "×";
        position: absolute;
        font-size: 20px;
        color: white;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize modal
    const toothDetailModal = new bootstrap.Modal(document.getElementById('toothDetailModal'));
    
    // Handle tooth clicks
    document.querySelectorAll('.tooth-container').forEach(tooth => {
        tooth.addEventListener('click', function() {
            const toothNumber = this.getAttribute('data-tooth-number');
            document.getElementById('modalToothNumber').textContent = toothNumber;
            document.getElementById('formToothNumber').value = toothNumber;
            
            // Here you would typically load existing data via AJAX
            // For now we'll just reset the form
            document.getElementById('toothCondition').value = 'healthy';
            document.getElementById('toothNotes').value = '';
            
            // Initialize Select2
            $('#toothProcedures').select2({
                placeholder: "Pilih prosedur",
                width: '100%',
                dropdownParent: $('#toothDetailModal')
            });
            
            toothDetailModal.show();
        });
    });
    
    // Handle form submission
    document.getElementById('toothForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        fetch(this.action, {
            method: 'POST',
            body: new FormData(this),
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                toothDetailModal.hide();
                location.reload(); // Refresh to show changes
            }
        })
        .catch(error => console.error('Error:', error));
    });
});
</script>
@endsection