@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="mb-4">Schedules</h3>
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

    <!-- Dropdown Pasien -->
    <form id="filterForm" action="{{ route('dashboard.schedules.get-doctors-by-date') }}" method="GET">
        <div class="form-group">
            <label for="patient">Select Patient:</label>
            <select id="patient" name="patient" class="form-control" required>
                <option value="">-- Select a Patient --</option>
            </select>
        </div>

        <div class="form-group mt-3">
            <label for="date">Select Date:</label>
            <input type="date" id="date" name="date" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Filter</button>
    </form>

    <div id="results" class="mt-4">
        <!-- Results will appear here -->
    </div>

    <div id="resultsClicked" class="mt-4">
        <!-- Results will appear here -->
    </div>

    <!-- Reservation Form (without AJAX) -->
    <form id="reservationForm" action="{{ route('dashboard.schedules.store-reservation') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="patient_id" id="patient_id">
        <input type="hidden" name="doctor_id" id="doctor_id">
        <input type="hidden" name="tanggal_reservasi" id="reservation_date">
        <input type="hidden" name="jam_mulai" id="time_start">
        <input type="hidden" name="jam_selesai" id="time_end">
        
        <button type="submit" class="btn btn-success mt-3">Make Reservation</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    // ambil data dari form
    const patientSelect = document.getElementById('patient');
    const reservationForm = document.getElementById('reservationForm');
    const resultsDiv = document.getElementById('results');
    const resultsClickedDiv = document.getElementById('resultsClicked');
    let selectedSchedule = null; // buat menyimpan jadwal yang dipilih

    // fetch isi dropdown
    try {
        const response = await fetch('/dashboard/schedules/get-patients'); // Fetch data pasien dari API
        const patients = await response.json(); // Konversi response ke JSON

        // Looping untuk menambahkan opsi ke dropdown pasien
        patients.forEach(patient => {
            const option = document.createElement('option');
            option.value = patient.id; // Set nilai option ke ID pasien
            option.textContent = patient.name; // Set teks option ke nama pasien
            patientSelect.appendChild(option); // Tambahkan option ke dropdown pasien
        });
    } catch (error) {
        console.error('Error fetching patients:', error); // Jika terjadi error, tampilkan di console
    }

    // jadwal dokter berdasarkan tanggal
    document.getElementById('filterForm').addEventListener('submit', async function(e) {
        e.preventDefault(); // Mencegah reload halaman saat submit form

        const date = document.getElementById('date').value; // Ambil tanggal dari input
        const patientId = document.getElementById('patient').value; // Ambil ID pasien yang dipilih

        if (!patientId) { // Jika pasien belum dipilih, tampilkan peringatan
            alert('Please select a patient first.');
            return;
        }

        try {
            const response = await fetch(`/dashboard/schedules/get-doctors-by-date?date=${date}`); // Fetch jadwal dokter berdasarkan tanggal
            const data = await response.json(); // Konversi response ke JSON

            // Tampilkan daftar jadwal dokter dalam tabel
            resultsDiv.innerHTML = ` 
                <h5>Jadwal Tersedia untuk ${data.date} (${data.day_of_week}):</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nama Dokter</th>
                                <th>Jam Tersedia</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.doctors.map(doctor => `
                                <tr data-doctor-id="${doctor.doctor.id}">
                                    <td><strong>${doctor.doctor.name}</strong></td> 
                                    <td>
                                        ${doctor.schedules.filter(schedule => schedule.is_available).map(schedule => 
                                            `<span class="badge bg-success schedule-badge" 
                                                  data-time-start="${schedule.time_start}" 
                                                  data-time-end="${schedule.time_end}" 
                                                  data-doctor-id="${doctor.doctor.id}" 
                                                  style="cursor:pointer;">
                                                ${schedule.time_start} - ${schedule.time_end}
                                            </span>`
                                        ).join(' ')}
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;

        } catch (error) {
            console.error('Error:', error); // Jika terjadi error, tampilkan di console
            resultsDiv.innerHTML = '<p class="text-danger">Terjadi kesalahan saat mengambil data jadwal</p>';
        }
    });

    /** 🔹 Event klik pada jadwal (badge) untuk memilih jadwal */
    resultsDiv.addEventListener('click', function(event) {
        if (event.target.classList.contains('schedule-badge')) { // Pastikan yang diklik adalah elemen dengan class 'schedule-badge'
            const selectedTimeStart = event.target.dataset.timeStart; // Ambil jam mulai dari badge
            const selectedTimeEnd = event.target.dataset.timeEnd; // Ambil jam selesai dari badge
            const doctorId = event.target.dataset.doctorId; // Ambil ID dokter dari badge

            // Simpan jadwal yang dipilih ke dalam variabel
            selectedSchedule = {
                time_start: selectedTimeStart,
                time_end: selectedTimeEnd,
                doctor_id: doctorId,
                date: document.getElementById('date').value
            };

            // Set nilai pada input hidden form reservasi
            document.getElementById('patient_id').value = document.getElementById('patient').value;
            document.getElementById('doctor_id').value = selectedSchedule.doctor_id;
            document.getElementById('reservation_date').value = selectedSchedule.date;
            document.getElementById('time_start').value = selectedSchedule.time_start;
            document.getElementById('time_end').value = selectedSchedule.time_end;

            // Tampilkan jadwal yang telah dipilih di bagian konfirmasi
            resultsClickedDiv.innerHTML = `
                <div class="alert alert-info mt-3">
                    <h5>Konfirmasi Jadwal yang Dipilih</h5>
                    <p><strong>Tanggal:</strong> ${selectedSchedule.date}</p>
                    <p><strong>Jam:</strong> ${selectedSchedule.time_start} - ${selectedSchedule.time_end}</p>
                    <p><strong>Dokter:</strong> ID ${selectedSchedule.doctor_id}</p>
                </div>
            `;

            // Tampilkan form reservasi
            reservationForm.style.display = 'block';
        }
    });

});
</script>

@endsection
