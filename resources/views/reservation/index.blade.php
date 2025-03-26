@extends('layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="mb-4 text-center">Make a Reservation</h3>
    <!-- Form untuk memilih tanggal -->
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Menampilkan pesan sukses jika ada reservasi yang berhasil dibuat -->
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif
            <form id="filterForm" class="p-4 border rounded">
                <div class="form-group">
                    <label for="date">Select Date:</label>
                    <input type="date" id="date" name="date" class="form-control" min="{{ date('Y-m-d') }}" required>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Find Available Schedules</button>
            </form>
            <!-- Hasil Jadwal Dokter -->
            <div id="results" class="mt-4">
                
            </div>
        </div>

    </div>



    <!-- Modal Konfirmasi -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirm Your Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Doctor:</strong> <span id="confirmDoctor"></span></p>
                    <p><strong>Date:</strong> <span id="confirmDate"></span></p>
                    <p><strong>Time:</strong> <span id="confirmTime"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="confirmForm" action="{{ route('reservation.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="patient_id" value="{{ auth()->id() }}">
                        <input type="hidden" name="doctor_id" id="confirmDoctorId">
                        <input type="hidden" name="tanggal_reservasi" id="confirmDateInput">
                        <input type="hidden" name="jam_mulai" id="confirmTimeStart">
                        <input type="hidden" name="jam_selesai" id="confirmTimeEnd">
                        <button type="submit" class="btn btn-success">Confirm Reservation</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.getElementById('filterForm'); // Form pencarian
        const resultsDiv = document.getElementById('results'); // Hasil jadwal
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal')); // Modal Bootstrap

        // Event saat mencari jadwal dokter
        filterForm.addEventListener('submit', async function(event) {
            event.preventDefault(); // Hindari refresh halaman

            const date = document.getElementById('date').value; // Ambil tanggal yang dipilih
            if (!date) {
                alert('Please select a date first.');
                return;
            }

            try {
                // Fetch data jadwal dokter berdasarkan tanggal
                const response = await fetch(`/dashboard/schedules/get-doctors-by-date?date=${date}`);
                if (!response.ok) throw new Error('Failed to fetch schedules.');

                const data = await response.json();
                renderSchedules(data);
            } catch (error) {
                console.error('Error fetching schedules:', error);
                resultsDiv.innerHTML = '<div class="alert alert-danger">Failed to load schedules.</div>';
            }
        });

        // Fungsi untuk menampilkan hasil pencarian jadwal dokter
        function renderSchedules(data) {
            if (!data.doctors || data.doctors.length === 0) {
                resultsDiv.innerHTML = `<div class="alert alert-info">No available schedules found for ${data.date}.</div>`;
                return;
            }

            let tableHTML = `
            <h5>Schedules for ${data.date} (${data.day_of_week}):</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Available Times</th>
                    </tr>
                </thead>
                <tbody>
        `;

            data.doctors.forEach(doctorSchedules => {
                tableHTML += `
                <tr>
                    <td>${doctorSchedules.doctor.name}</td>
                    <td>
            `;
                doctorSchedules.schedules.forEach(time => {
                    if (time.is_available) {
                        tableHTML += `
                        <button type="button" class="btn btn-success btn-sm select-time"
                                data-doctor-id="${doctorSchedules.doctor.id}"
                                data-doctor-name="${doctorSchedules.doctor.name}"
                                data-date="${data.date}"
                                data-time-start="${time.time_start}"
                                data-time-end="${time.time_end}">
                            ${time.time_start} - ${time.time_end}
                        </button>
                    `;
                    }
                });
                tableHTML += `</td></tr>`;
            });

            tableHTML += `</tbody></table>`;
            resultsDiv.innerHTML = tableHTML;

            // Tambahkan event listener untuk semua tombol yang baru dibuat
            document.querySelectorAll('.select-time').forEach(button => {
                button.addEventListener('click', function() {
                    // Ambil data dari button yang diklik
                    const doctorId = this.dataset.doctorId;
                    const doctorName = this.dataset.doctorName;
                    const date = this.dataset.date;
                    const timeStart = this.dataset.timeStart;
                    const timeEnd = this.dataset.timeEnd;

                    // Set data ke dalam modal konfirmasi
                    document.getElementById('confirmDoctor').textContent = doctorName;
                    document.getElementById('confirmDate').textContent = date;
                    document.getElementById('confirmTime').textContent = `${timeStart} - ${timeEnd}`;

                    // Set input hidden untuk form reservasi
                    document.getElementById('confirmDoctorId').value = doctorId;
                    document.getElementById('confirmDateInput').value = date;
                    document.getElementById('confirmTimeStart').value = timeStart;
                    document.getElementById('confirmTimeEnd').value = timeEnd;

                    // Tampilkan modal
                    confirmModal.show();
                });
            });
        }
    });
</script>
@endsection