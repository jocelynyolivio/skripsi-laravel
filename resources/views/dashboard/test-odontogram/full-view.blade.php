@extends('dashboard.layouts.main')

@section('container')
<style>
  .odontogram-container {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .tooth-row {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 10px;
  }

  .odontogram-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 70px;
    height: 90px;
    border: 1px solid #ddd;
    background-color: white;
    margin: 2px;
  }

  .odontogram-svg {
    width: 100%;
    height: 80px;
  }

  .tooth-number {
    font-weight: bold;
    font-size: 12px;
    text-align: center;
  }

  .tooth-part {
    transition: fill 0.2s;
    cursor: pointer;
  }

  .tooth-part:hover {
    fill: #ddd !important;
  }

  #tooth-info {
    text-align: center;
    margin-top: 20px;
    font-weight: bold;
    min-height: 24px;
  }

  .legend-box {
    display: inline-block;
    width: 20px;
    height: 20px;
    margin-right: 6px;
    border: 1px solid #ccc;
    vertical-align: middle;
  }
</style>

<div class="container mt-4">
  <h3 class="text-center">Odontogram View</h3>
  @if(session('success'))
  <div class="alert alert-success">
    {{ session('success') }}
  </div>
  @endif

  @if(session('error'))
  <div class="alert alert-danger">
    {{ session('error') }}
  </div>
  @endif
  <div class="mb-4 text-center">
    <div class="d-flex justify-content-center flex-wrap gap-3">
      <div><span class="legend-box" style="background-color: #e74c3c;"></span> Karies</div>
      <div><span class="legend-box" style="background-color: #95a5a6;"></span> Tambalan</div>
      <div><span class="legend-box" style="background-color: #f39c12;"></span> Fraktur</div>
      <div><span class="legend-box" style="background-color: #27ae60;"></span> Kalkulus</div>
      <div><span class="legend-box" style="background-color: #8e44ad;"></span> Impaksi</div>
      <div><span class="legend-box" style="background-color: #3498db;"></span> Abrasi</div>
    </div>
  </div>


  <div class="odontogram-container">

    {{-- Baris 1: Gigi Tetap Atas (18–11 | 21–28) --}}
    <div class="tooth-row">
      @foreach(['18','17','16','15','14','13','12','11','21','22','23','24','25','26','27','28'] as $tooth)
      @include('dashboard.test-odontogram.partials.tooth-svg', ['toothNumber' => $tooth])
      @endforeach
    </div>

    {{-- Baris 2: Gigi Susu Atas (55–51 | 61–65) --}}
    <div class="tooth-row">
      @foreach(['55','54','53','52','51','61','62','63','64','65'] as $tooth)
      @include('dashboard.test-odontogram.partials.tooth-svg', ['toothNumber' => $tooth])
      @endforeach
    </div>

    {{-- Baris 3: Gigi Susu Bawah (85–81 | 71–75) --}}
    <div class="tooth-row">
      @foreach(['85','84','83','82','81','71','72','73','74','75'] as $tooth)
      @include('dashboard.test-odontogram.partials.tooth-svg', ['toothNumber' => $tooth])
      @endforeach
    </div>

    {{-- Baris 4: Gigi Tetap Bawah (48–41 | 31–38) --}}
    <div class="tooth-row">
      @foreach(['48','47','46','45','44','43','42','41','31','32','33','34','35','36','37','38'] as $tooth)
      @include('dashboard.test-odontogram.partials.tooth-svg', ['toothNumber' => $tooth])
      @endforeach
    </div>

  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="toothInfoModal" tabindex="-1" aria-labelledby="toothInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered"> <!-- Diperbesar -->
    <form id="condition-form" method="POST" action="{{ route('odontogram.store', $patientId) }}">
      @csrf
      <div class="modal-content" style="width: 500px;">
        <div class="modal-header">
          <h5 class="modal-title" id="toothInfoModalLabel">Input Tooth Condition</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="tooth_number" id="input-tooth_number">
          <input type="hidden" name="surface" id="input-surface">

          <div class="mb-3">
            <label class="form-label">Tooth number</label>
            <input type="text" class="form-control" id="tooth-display" readonly>
          </div>

          <div class="mb-3">
            <label class="form-label">Surface</label>
            <input type="text" class="form-control" id="surface-display" readonly>
          </div>

          <div class="mb-3">
            <label for="condition" class="form-label">Condition</label>
            <select name="condition" id="condition" class="form-select" required>
              <option value="">-- Pilih --</option>
              <option value="karies">Karies</option>
              <option value="tambalan">Tambalan</option>
              <option value="fraktur">Fraktur</option>
              <option value="kalkulus">Kalkulus</option>
              <option value="impaksi">Impaksi</option>
              <option value="abrasi">Abrasi</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>


<script>
  document.addEventListener('DOMContentLoaded', function() {
    const toothConditions = @json($toothConditions);

    const conditionColors = {
      karies: "#e74c3c",
      tambalan: "#95a5a6",
      fraktur: "#f39c12",
      kalkulus: "#27ae60",
      impaksi: "#8e44ad",
      abrasi: "#3498db"
    };

    // Warnai sesuai kondisi sebelumnya
    Object.entries(toothConditions).forEach(([key, condition]) => {
      const [part, number] = key.split(' ');
      const selector = `.odontogram-card[data-tooth="${number}"] .tooth-part[data-part="${part}"]`;
      const element = document.querySelector(selector);
      if (element && conditionColors[condition]) {
        element.style.fill = conditionColors[condition];
      }
    });

    // Tangkap klik surface gigi
    document.querySelectorAll('.tooth-part').forEach(part => {
      part.addEventListener('click', function() {
        const surface = this.dataset.part;
        const toothNumber = this.closest('.odontogram-card').dataset.tooth;

        // Set input form
        document.getElementById('input-surface').value = surface;
        document.getElementById('input-tooth_number').value = toothNumber;

        document.getElementById('surface-display').value = surface;
        document.getElementById('tooth-display').value = toothNumber;

        // Reset select & notes
        document.getElementById('condition').value = "";
        document.getElementById('notes').value = "";

        // Tampilkan modal
        const modal = new bootstrap.Modal(document.getElementById('toothInfoModal'));
        modal.show();
      });
    });
  });
</script>
@endsection