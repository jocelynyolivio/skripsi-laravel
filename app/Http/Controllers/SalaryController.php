<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SalaryController extends Controller
{
    public function uploadForm()
    {
        return view('dashboard.salaries.upload-salary');
    }

    // public function processExcel(Request $request)
    // {
    //     // Validasi file upload
    //     $request->validate([
    //         'file' => 'required|mimes:xls,xlsx',
    //     ]);

    //     // Ambil path file yang diunggah
    //     $filePath = $request->file('file')->getPathName();

    //     // Load file XLS/XLSX menggunakan PhpSpreadsheet
    //     $spreadsheet = IOFactory::load($filePath);

    //     // Daftar nama sheet yang ingin dibaca
    //     $sheetNames = ['1.2.3', '4.5.6', '7.8.9', '10.11.12', '13.14.15', '16.17'];
    //     $dataSummary = [];

    //     // Kolom awal data untuk setiap dokter
    //     $startingColumns = [
    //         ['departemen' => 'B', 'nama' => 'J', 'data_awal' => 'B'], // Dokter 1
    //         ['departemen' => 'Q', 'nama' => 'Y', 'data_awal' => 'Q'], // Dokter 2
    //         ['departemen' => 'AF', 'nama' => 'AN', 'data_awal' => 'AF'], // Dokter 3
    //     ];

    //     foreach ($sheetNames as $sheetName) {
    //         // Ambil sheet berdasarkan nama
    //         $sheet = $spreadsheet->getSheetByName($sheetName);
    //         if (!$sheet) continue; // Lewati jika sheet tidak ditemukan

    //         foreach ($startingColumns as $columns) {
    //             // Ambil informasi statis dokter dari sheet
    //             $departemen = $sheet->getCell($columns['departemen'] . "3")->getValue(); // Departemen dokter
    //             $nama = $sheet->getCell($columns['nama'] . "3")->getValue(); // Nama dokter
    //             $noId = $sheet->getCell($columns['nama'] . "4")->getValue(); // ID dokter

    //             $kehadiran = []; // Array untuk menyimpan data kehadiran

    //             for ($row = 12; $row <= 42; $row++) { // Iterasi baris untuk membaca tanggal dan jam kerja
    //                 $tanggal = $sheet->getCell("A{$row}")->getValue(); // Ambil tanggal

    //                 if ($tanggal) {
    //                     $col = $columns['data_awal']; // Kolom awal berdasarkan dokter
    //                     $jamMasuk = $this->convertTime($sheet->getCell($col . $row)->getValue()); // Ambil jam masuk
    //                     $jamPulang = null; // Default null jika tidak ditemukan

    //                     // Loop untuk mencari jam pulang dengan memeriksa beberapa kolom setelah jam masuk
    //                     for ($offset = 1; $offset <= 3; $offset++) {
    //                         $colCheck = $this->getNextExcelColumn($col, $offset);
    //                         $jamCek = $this->convertTime($sheet->getCell($colCheck . $row)->getValue());
    //                         if ($jamCek && !$jamPulang) {
    //                             $jamPulang = $jamCek; // Simpan jam pulang pertama yang ditemukan
    //                         }
    //                     }

    //                     // Ambil data lembur berdasarkan kolom yang sesuai
    //                     $lemburMasuk = $this->convertTime($sheet->getCell($this->getNextExcelColumn($col, 4) . $row)->getValue());
    //                     $lemburPulang = $this->convertTime($sheet->getCell($this->getNextExcelColumn($col, 5) . $row)->getValue());

    //                     // Jika ada teks "Bolos", tandai sebagai tidak hadir
    //                     $status = (strtolower(trim($jamMasuk)) == 'bolos' || strtolower(trim($jamPulang)) == 'bolos') ? 'Bolos' : 'Hadir';

    //                     // Simpan data kehadiran dalam array
    //                     $kehadiran[] = [
    //                         'tanggal' => $tanggal,
    //                         'jam_masuk' => $jamMasuk,
    //                         'jam_pulang' => $jamPulang,
    //                         'lembur_masuk' => $lemburMasuk,
    //                         'lembur_pulang' => $lemburPulang,
    //                         'status' => $status,
    //                     ];
    //                 }
    //             }

    //             // Simpan semua data dokter ke dalam array
    //             $dataSummary[] = [
    //                 'sheet' => $sheetName,
    //                 'departemen' => $departemen,
    //                 'nama' => $nama,
    //                 'no_id' => $noId,
    //                 'kehadiran' => $kehadiran,
    //             ];
    //         }
    //     }

    //     // Kirim data ke view untuk ditampilkan
    //     return view('dashboard.salaries.salary-result', compact('dataSummary'));
    // }
    public function processExcel(Request $request)
    {
        // Validasi file upload
        $request->validate([
            'file' => 'required|mimes:xls,xlsx',
        ]);

        // Ambil path file yang diunggah
        $filePath = $request->file('file')->getPathName();

        // Load file XLS/XLSX menggunakan PhpSpreadsheet
        $spreadsheet = IOFactory::load($filePath);

        // Daftar nama sheet yang ingin dibaca
        $sheetNames = ['1.2.3', '4.5.6', '7.8.9', '10.11.12', '13.14.15', '16.17'];
        $dataSummary = [];

        // Kolom awal data untuk setiap dokter
        $startingColumns = [
            ['departemen' => 'B', 'nama' => 'J', 'jam_kerja_1' => 'B', 'jam_kerja_2' => 'G', 'lembur' => 'K'], // Dokter 1
            ['departemen' => 'Q', 'nama' => 'Y', 'jam_kerja_1' => 'Q', 'jam_kerja_2' => 'V', 'lembur' => 'Z'], // Dokter 2
            ['departemen' => 'AF', 'nama' => 'AN', 'jam_kerja_1' => 'AF', 'jam_kerja_2' => 'AK', 'lembur' => 'AO'], // Dokter 3
        ];

        foreach ($sheetNames as $sheetName) {
            // Ambil sheet berdasarkan nama
            $sheet = $spreadsheet->getSheetByName($sheetName);
            if (!$sheet) continue; // Lewati jika sheet tidak ditemukan

            foreach ($startingColumns as $columns) {
                // Ambil informasi statis dokter dari sheet
                $departemen = $sheet->getCell($columns['departemen'] . "3")->getValue(); // Departemen dokter
                $nama = $sheet->getCell($columns['nama'] . "3")->getValue(); // Nama dokter
                $noId = $sheet->getCell($columns['nama'] . "4")->getValue(); // ID dokter

                $kehadiran = []; // Array untuk menyimpan data kehadiran

                for ($row = 12; $row <= 42; $row++) { // Iterasi baris untuk membaca tanggal dan jam kerja
                    $tanggal = $sheet->getCell("A{$row}")->getValue(); // Ambil tanggal

                    if ($tanggal) {
                        $jamMasuk = null;
                        $jamPulang = null;

                        // **1. Cek Jam Kerja 1**
                        $jamMasuk = trim($this->convertTime($sheet->getCell($columns['jam_kerja_1'] . $row)->getValue()));
                        for ($offset = 1; $offset <= 3; $offset++) {
                            $colCheck = $this->getNextExcelColumn($columns['jam_kerja_1'], $offset);
                            $jamCek = trim($this->convertTime($sheet->getCell($colCheck . $row)->getValue()));
                            if ($jamCek && !$jamPulang) {
                                $jamPulang = $jamCek;
                            }
                        }

                        // **2. Jika Jam Kerja 1 kosong, cek Jam Kerja 2**
                        if (!$jamMasuk) {
                            $jamMasuk = trim($this->convertTime($sheet->getCell($columns['jam_kerja_2'] . $row)->getValue()));
                            for ($offset = 1; $offset <= 3; $offset++) {
                                $colCheck = $this->getNextExcelColumn($columns['jam_kerja_2'], $offset);
                                $jamCek = trim($this->convertTime($sheet->getCell($colCheck . $row)->getValue()));
                                if ($jamCek && !$jamPulang) {
                                    $jamPulang = $jamCek;
                                }
                            }
                        }

                        // **3. Jika masih kosong, cek Lembur**
                        if (!$jamMasuk) {
                            $jamMasuk = trim($this->convertTime($sheet->getCell($columns['lembur'] . $row)->getValue()));
                            for ($offset = 1; $offset <= 3; $offset++) {
                                $colCheck = $this->getNextExcelColumn($columns['lembur'], $offset);
                                $jamCek = trim($this->convertTime($sheet->getCell($colCheck . $row)->getValue()));
                                if ($jamCek && !$jamPulang) {
                                    $jamPulang = $jamCek;
                                }
                            }
                        }

                        // **Cek apakah jam masuk atau jam pulang berisi "Bolos"**
                        if (
                            strtolower($jamMasuk) == 'bolos' || strtolower($jamPulang) == 'bolos' ||
                            empty($jamMasuk) && empty($jamPulang) // Jika keduanya kosong, skip
                        ) {
                            continue; // Lewati data ini
                        }

                        // Simpan data kehadiran dalam array
                        $kehadiran[] = [
                            'tanggal' => $tanggal,
                            'jam_masuk' => $jamMasuk,
                            'jam_pulang' => $jamPulang,
                        ];
                    }
                }

                // Ambil periode dari cell D2
                $periode = $sheet->getCell("D2")->getValue();
                $periodeParts = explode(' ', trim($periode));
                $tanggalRange = explode('-', $periodeParts[0] ?? ''); // Ambil bagian sebelum "~"

                // Ambil Tahun dan Bulan dari periode
                $tahun = $tanggalRange[0] ?? null;
                $bulan = $tanggalRange[1] ?? null;

                foreach ($kehadiran as $record) {
                    // **Hanya simpan jika ada jam masuk atau jam pulang**
                    if (empty($record['jam_masuk']) && empty($record['jam_pulang'])) {
                        continue;
                    }

                    // Ambil hanya angka dari tanggal (misalnya '02' dari '02 SEN')
                    $tanggalParts = explode(' ', trim($record['tanggal']));
                    $tanggalOnly = $tanggalParts[0] ?? null;

                    // Format tanggal ke YYYY-MM-DD
                    $tanggalFinal = ($tanggalOnly && $tahun && $bulan)
                        ? "{$tahun}-{$bulan}-" . str_pad($tanggalOnly, 2, '0', STR_PAD_LEFT)
                        : null;

                    // Cek apakah tanggal valid
                    if (!$tanggalFinal) {
                        continue;
                    }

                    // Simpan ke database
                    Attendance::create([
                        'no_id' => $noId,
                        'nama' => $nama,
                        'tanggal' => $tanggalFinal,
                        'jam_masuk' => $record['jam_masuk'] ? \Carbon\Carbon::parse($record['jam_masuk'])->format('H:i:s') : null,
                        'jam_pulang' => $record['jam_pulang'] ? \Carbon\Carbon::parse($record['jam_pulang'])->format('H:i:s') : null,
                    ]);
                }

                // Simpan semua data dokter ke dalam array
                $dataSummary[] = [
                    'sheet' => $sheetName,
                    'departemen' => $departemen,
                    'nama' => $nama,
                    'no_id' => $noId,
                    'kehadiran' => $kehadiran,
                ];
            }
        }

        // Kirim data ke view untuk ditampilkan
        return view('dashboard.salaries.salary-result', compact('dataSummary'));
    }




    // Fungsi untuk mengonversi format waktu dari angka Excel ke format jam
    private function convertTime($value)
    {
        if (is_numeric($value)) {
            $hours = floor($value * 24);
            $minutes = round(($value * 1440) % 60);
            return sprintf('%02d:%02d', $hours, $minutes);
        }
        return $value ? trim($value) : null; // Jika bukan angka, kembalikan nilai asli
    }

    // Fungsi untuk mendapatkan kolom Excel berikutnya dengan mendukung format lebih dari satu huruf
    private function getNextExcelColumn($col, $offset = 1)
    {
        $letters = str_split($col);
        $length = count($letters);

        for ($i = $length - 1; $i >= 0; $i--) {
            if ($letters[$i] !== 'Z') {
                $letters[$i] = chr(ord($letters[$i]) + $offset);
                return implode('', $letters);
            }
            $letters[$i] = 'A';
        }
        return 'A' . implode('', $letters);
    }
}
