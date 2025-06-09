<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReservationInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $reservationDetails;
    public $googleCalendarLink; // Properti baru untuk menyimpan link

    public function __construct($reservationDetails)
    {
        $this->reservationDetails = $reservationDetails;
        $this->googleCalendarLink = $this->generateGoogleCalendarLink($reservationDetails); // Panggil fungsi pembuat link
    }

    public function build()
    {
        // Tetap kirim ICS attachment sebagai fallback atau untuk aplikasi kalender lain
        $icsContent = $this->generateIcsContent();
        $filename = 'reservasi_' . now()->format('YmdHis') . '.ics';

        return $this->subject('Undangan Reservasi Anda')
                    ->view('emails.reservation_invitation') // View ini akan menampilkan link
                    ->attachData($icsContent, $filename, [
                        'mime' => 'text/calendar; charset=utf-8; method=REQUEST'
                    ]);
    }

    protected function generateGoogleCalendarLink($details)
    {
        $start = Carbon::parse($details['start_time']);
        $end = Carbon::parse($details['end_time']);

        // Format waktu untuk Google Calendar (YYYYMMDDTHHMMSSZ atau YYYYMMDDTHHMMSS/YYYYMMDDTHHMMSS)
        // Kita gunakan format lokal tanpa Z karena bukan UTC, atau bisa konversi ke UTC
        $startFormatted = $start->format('Ymd\THis');
        $endFormatted = $end->format('Ymd\THis');

        $title = urlencode($details['title']);
        $description = urlencode($details['description']);
        $location = urlencode('Online Meeting' ?? ''); // Opsional: tambahkan lokasi jika ada
        $guests = urlencode($details['doctor_email']); // Hanya email dokter

        $link = "https://calendar.google.com/calendar/render?action=TEMPLATE";
        $link .= "&text=" . $title;
        $link .= "&dates=" . $startFormatted . "/" . $endFormatted;
        $link .= "&details=" . $description;
        $link .= "&location=" . $location;
        $link .= "&add=" . $guests; // Mengundang email tertentu

        return $link;
    }

    // Fungsi generateIcsContent() tetap sama seperti sebelumnya
    protected function generateIcsContent()
    {
        $start = Carbon::parse($this->reservationDetails['start_time'])->format('Ymd\THis');
        $end = Carbon::parse($this->reservationDetails['end_time'])->format('Ymd\THis');
        $title = $this->reservationDetails['title'] ?? 'Reservasi Skripsi';
        $description = $this->reservationDetails['description'];
        $doctorEmail = $this->reservationDetails['doctor_email'];
        $doctorName = $this->reservationDetails['doctor_name'];

        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//Laracasts/Laravel//NONSGML v1.0//EN\r\n";
        $ics .= "CALSCALE:GREGORIAN\r\n";
        $ics .= "BEGIN:VEVENT\r\n";
        $ics .= "DTSTAMP:" . now()->format('Ymd\THis\Z') . "\r\n";
        $ics .= "UID:" . uniqid() . "@yourdomain.com\r\n"; // GANTI DENGAN DOMAIN APLIKASI ANDA!
        $ics .= "DTSTART:" . $start . "\r\n";
        $ics .= "DTEND:" . $end . "\r\n";
        $ics .= "SUMMARY:" . $title . "\r\n";
        $ics .= "DESCRIPTION:" . $description . "\r\n";
        $ics .= "ATTENDEE;CN=\"" . $doctorName . "\";RSVP=TRUE:mailto:" . $doctorEmail . "\r\n";
        $ics .= "END:VEVENT\r\n";
        $ics .= "END:VCALENDAR\r\n";

        return $ics;
    }
}