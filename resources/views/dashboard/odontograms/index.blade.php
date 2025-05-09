@extends('dashboard.layouts.main')
@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['text' => 'Master Patients', 'url' => route('dashboard.masters.patients')],
['text' => 'Kondisi Gigi '. $patient->fname. ' '. $patient->mname. ' '. $patient->lname]
]
])
@endsection
@section('container')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0 text-center">Kondisi Gigi : {{ $patient->fname }} {{ $patient->mname }} {{ $patient->lname }}</h4>
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
                <div class="child upper-jaw d-flex justify-content-center">
                    <div class="quadrant me-4">
                        @foreach([55,54,53,52,51] as $toothNumber)
                        @include('dashboard.odontograms.tooth-button', ['toothNumber' => $toothNumber])
                        @endforeach
                    </div>

                    <div class="quadrant">
                        @foreach([61,62,63,64,65] as $toothNumber)
                        @include('dashboard.odontograms.tooth-button', ['toothNumber' => $toothNumber])
                        @endforeach
                    </div>
                </div>

                <div class="child lower-jaw d-flex justify-content-center">
                    <div class="quadrant me-4">
                        @foreach([85,84,83,82,81] as $toothNumber)
                        @include('dashboard.odontograms.tooth-button', ['toothNumber' => $toothNumber])
                        @endforeach
                    </div>

                    <div class="quadrant">
                        @foreach([71,72,73,74,75] as $toothNumber)
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
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
                    <div class="col d-flex align-items-center gap-2">
                        <span class="symbol healthy"></span> Sehat
                    </div>
                    <div class="col d-flex align-items-center gap-2">
                        <span class="symbol cavity"></span> Karies
                    </div>
                    <div class="col d-flex align-items-center gap-2">
                        <span class="symbol filled"></span> Tambalan
                    </div>
                    <div class="col d-flex align-items-center gap-2">
                        <span class="symbol extracted"></span> Dicabut
                    </div>
                    <div class="col d-flex align-items-center gap-2">
                        <span class="symbol temporary-filling"></span> Tambalan Sementara
                    </div>
                    <div class="col d-flex align-items-center gap-2">
                        <span class="symbol extraction-needed"></span> Perlu Dicabut
                    </div>
                    <div class="col d-flex align-items-center gap-2">
                        <span class="symbol missing"></span> Hilang
                    </div>
                    <div class="col d-flex align-items-center gap-2">
                        <span class="symbol impacted"></span> Impaksi
                    </div>
                    <div class="col d-flex align-items-center gap-2">
                        <span class="symbol root-remnants"></span> Sisa Akar
                    </div>
                    <div class="col d-flex align-items-center gap-2">
                        <span class="symbol fractured"></span> Fraktur
                    </div>
                    <div class="col d-flex align-items-center gap-2">
                        <span class="symbol sealant"></span> Sealant
                    </div>
                    <div class="col d-flex align-items-center gap-2">
                        <span class="symbol unerupted"></span> Belum Tumbuh
                    </div>
                    <div class="col d-flex align-items-center gap-2">
                        <span class="symbol crown"></span> Mahkota
                    </div>
                    <div class="col d-flex align-items-center gap-2">
                        <span class="symbol bridge"></span> Jembatan
                    </div>
                    <div class="col d-flex align-items-center gap-2">
                        <span class="symbol prosthesis"></span> Gigi Tiruan
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
                                <option value="cavity">Karies (Berlubang)</option>
                                <option value="filled">Tambalan</option>
                                <option value="temporary_filling">Tambalan Sementara</option>
                                <option value="extracted">Sudah Dicabut</option>
                                <option value="extraction_needed">Perlu Dicabut</option>
                                <option value="missing">Gigi Hilang</option>
                                <option value="impacted">Impaksi</option>
                                <option value="root_remnants">Sisa Akar</option>
                                <option value="fractured">Patah</option>
                                <option value="sealant">Sealant</option>
                                <option value="un_erupted">Belum Tumbuh</option>
                                <option value="crown">Mahkota Buatan</option>
                                <option value="bridge">Jembatan Gigi</option>
                                <option value="prosthesis">Gigi Tiruan</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="surface" class="form-label">Tooth Surface</label>
                        <select name="surface[]" id="surface" class="form-select" multiple>
                            <option value="M">Mesial (M)</option>
                            <option value="O">Occlusal (O)</option>
                            <option value="L">Lingual (L)</option>
                            <option value="D">Distal (D)</option>
                            <option value="B">Buccal (B)</option>
                            <option value="I">Incisal (I)</option>
                            <option value="C">Cervical (C)</option>
                        </select>
                        <small class="text-muted">Tekan CTRL (Windows) atau Command (Mac) untuk memilih lebih dari satu.</small>
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
        padding-bottom: 30px;
    }

    .lower-jaw {
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
    /* Condition Styles */
    .healthy .tooth-shape {
        background-color: #f8f9fa;
    }

    .cavity .tooth-shape {
        background: repeating-linear-gradient(45deg,
                #f8f9fa,
                #f8f9fa 5px,
                #ffc107 5px,
                #ffc107 10px);
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

    .temporary-filling .tooth-shape {
        background-color: #fd7e14;
    }

    .extraction-needed .tooth-shape {
        background-color: #dc3545;
    }

    .missing .tooth-shape {
        background-color: #dee2e6;
    }

    .impacted .tooth-shape {
        background-color: #17a2b8;
    }

    .root-remnants .tooth-shape {
        background-color: #c0392b;
    }

    .fractured .tooth-shape {
        background-color: #e67e22;
    }

    .sealant .tooth-shape {
        background-color: #00bcd4;
    }

    .unerupted .tooth-shape {
        background-color: #673ab7;
    }

    .crown .tooth-shape {
        background-color: #fbc02d;
    }

    .bridge .tooth-shape {
        background-color: #e91e63;
    }

    .prosthesis .tooth-shape {
        background-color: #4caf50;
    }

    /* Prosedur indikator */
    .has-procedure .tooth-shape {
        border: 3px solid #198754;
    }


    /* Legend Styles */
    .symbol {
        width: 25px;
        height: 30px;
        border: 1px solid #333;
        border-radius: 3px;
        display: inline-block;
        margin-right: 10px;
        position: relative;
    }

    .symbol.healthy {
        background-color: #f8f9fa;
    }

    .symbol.cavity {
        background: repeating-linear-gradient(45deg, #f8f9fa, #f8f9fa 3px, #ffc107 3px, #ffc107 6px);
    }

    .symbol.filled {
        background-color: #0dcaf0;
    }

    .symbol.extracted {
        background-color: #6c757d;
    }

    .symbol.extracted::after {
        content: "×";
        font-size: 20px;
        color: white;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .symbol.temporary-filling {
        background-color: #fd7e14;
    }

    .symbol.extraction-needed {
        background-color: #dc3545;
    }

    .symbol.missing {
        background-color: #dee2e6;
    }

    .symbol.impacted {
        background-color: #17a2b8;
    }

    .symbol.root-remnants {
        background-color: #c0392b;
    }

    .symbol.fractured {
        background-color: #e67e22;
    }

    .symbol.sealant {
        background-color: #00bcd4;
    }

    .symbol.unerupted {
        background-color: #673ab7;
    }

    .symbol.crown {
        background-color: #fbc02d;
    }

    .symbol.bridge {
        background-color: #e91e63;
    }

    .symbol.prosthesis {
        background-color: #4caf50;
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
                document.getElementById('surface').value = '';

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
        document.getElementById('toothForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Terjadi kesalahan');
                }

                if (data.success) {
                    // Refresh halaman setelah 1 detik
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showError(data.message || 'Gagal menyimpan data');
                }
            } catch (error) {
                console.error('Error:', error);
                showError(error.message || 'Terjadi kesalahan saat menyimpan data');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    });

    function showError(message) {
        // Hapus error sebelumnya jika ada
        const existingAlert = document.querySelector('.ajax-error-alert');
        if (existingAlert) {
            existingAlert.remove();
        }

        // Buat elemen alert baru
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger ajax-error-alert mt-3';
        alertDiv.innerHTML = `
        <strong>Error!</strong> ${message}
        <button type="button" class="btn-close float-end" data-bs-dismiss="alert"></button>
    `;

        // Tambahkan alert ke modal
        const modalBody = document.querySelector('#toothDetailModal .modal-body');
        modalBody.prepend(alertDiv);

        // Auto close setelah 5 detik
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
</script>
@endsection