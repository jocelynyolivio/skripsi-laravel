@extends('dashboard.layouts.main')

@section('container')
<style>
  .odontogram-card {
    width: 80px;
    height: 80px;
    border: 1px solid #ccc;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    margin: 20px;
  }

  .odontogram-svg {
    width: 100%;
    height: 100%;
  }
</style>

<div class="odontogram-card">
  <svg class="odontogram-svg" viewBox="0 0 200 200" id="odontogram-svg">
    <polygon id="trapesium-atas" points="40,40 160,40 120,80 80,80" fill="#e74c3c" />
    <polygon id="trapesium-kanan" points="160,40 160,160 120,120 120,80" fill="#27ae60" />
    <polygon id="trapesium-bawah" points="40,160 160,160 120,120 80,120" fill="#3498db" />
    <polygon id="trapesium-kiri" points="40,40 40,160 80,120 80,80" fill="#27ae60" />
    <rect id="kotak" x="80" y="80" width="40" height="40" fill="white" stroke="#333" stroke-width="1" />
  </svg>
</div>

<div class="odontogram-card">
  <svg class="odontogram-svg" viewBox="0 0 200 200" id="odontogram-svg-2">
    <polygon id="trapesium-atas-2" points="60,40 140,40 120,80 80,80" fill="#e74c3c" />
    <polygon id="trapesium-kanan-2" points="140,40 140,160 120,120 120,80" fill="#27ae60" />
    <polygon id="trapesium-bawah-2" points="60,160 140,160 120,120 80,120" fill="#3498db" />
    <polygon id="trapesium-kiri-2" points="60,40 60,160 80,120 80,80" fill="#27ae60" />
    <rect id="kotak-2" x="80" y="80" width="40" height="40" fill="white" stroke="#333" stroke-width="1" />
  </svg>
</div>

<div class="odontogram-card">
  <svg class="odontogram-svg" viewBox="0 0 200 200" id="odontogram-svg-3">
    <polygon id="trapesium-atas-3" points="40,40 160,40 120,90 80,90" fill="#e74c3c" />
    <polygon id="trapesium-kanan-3" points="160,40 160,160 120,90 120,90" fill="#27ae60" />
    <polygon id="trapesium-bawah-3" points="40,160 160,160 120,90 80,90" fill="#3498db" />
    <polygon id="trapesium-kiri-3" points="40,40 40,160 80,90 80,90" fill="#27ae60" />
  </svg>
</div>

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

<script>
  function handleClick(event) {
    const target = event.target;
    if (['polygon', 'rect', 'path', 'circle'].includes(target.tagName)) {
      const id = target.id;
      if (id) {
        alert("ID elemen: " + id);
      } else {
        alert("Elemen ini tidak memiliki ID.");
      }
    }
  }

  document.getElementById("odontogram-svg").addEventListener("click", handleClick);
  document.getElementById("odontogram-svg-2").addEventListener("click", handleClick);
  document.getElementById("odontogram-svg-3").addEventListener("click", handleClick);
</script>


@endsection