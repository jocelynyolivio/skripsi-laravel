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
</style>

<div class="container mt-4">
  <h3 class="text-center">Odontogram View</h3>
  <div id="tooth-info">Klik bagian gigi untuk melihat detail</div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Fungsi untuk menangani klik pada bagian gigi
  function handleToothPartClick() {
    const toothNumber = this.parentElement.parentElement.getAttribute('data-tooth');
    const partName = this.getAttribute('data-part');
    document.getElementById('tooth-info').textContent = `${partName} - ${toothNumber}`;
    
    // Reset semua warna
    document.querySelectorAll('.tooth-part').forEach(part => {
      part.style.fill = 'white';
    });
    
    // Highlight bagian yang diklik
    this.style.fill = '#ddd';
  }

  // Tambahkan event listener ke semua bagian gigi
  document.querySelectorAll('.tooth-part').forEach(part => {
    part.addEventListener('click', handleToothPartClick);
    part.style.cursor = 'pointer'; // Pastikan cursor berubah jadi pointer
  });
});
</script>
@endsection