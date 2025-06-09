<!DOCTYPE html>
<html>
<head>
    <title>Reservasi Anda</title>
</head>
<body>
    <p>Halo {{ $reservationDetails['doctor_name'] }},</p>
    <p>Anda memiliki reservasi baru:</p>
    <ul>
        <li><strong>Judul:</strong> {{ $reservationDetails['title'] }}</li>
        <li><strong>Tanggal & Waktu:</strong> {{ \Carbon\Carbon::parse($reservationDetails['start_time'])->format('d M Y H:i') }} - {{ \Carbon\Carbon::parse($reservationDetails['end_time'])->format('H:i') }}</li>
        <li><strong>Detail:</strong> {{ $reservationDetails['description'] }}</li>
    </ul>

    {{-- Tombol Add to Google Calendar --}}
    <p>
        Klik tombol di bawah ini untuk menambahkan acara ini langsung ke Google Calendar Anda:
        <br>
        <a href="{{ $googleCalendarLink }}" target="_blank" style="display: inline-block; padding: 10px 20px; background-color: #4285F4; color: white; text-decoration: none; border-radius: 5px;">
            Add to Google Calendar
        </a>
    </p>

    <p>Sebagai alternatif, Anda juga bisa menggunakan lampiran .ics yang disertakan dalam email ini.</p>
    <p>Terima kasih.</p>
</body>
</html>