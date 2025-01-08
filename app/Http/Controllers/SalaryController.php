<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

// class SalaryController extends Controller
// {
//     public function uploadForm()
//     {
//         return view('dashboard.salaries.upload-salary');
//     }

//     public function processExcel(Request $request)
//     {
//         // Validasi file upload
//         $request->validate([
//             'file' => 'required|mimes:xls,xlsx',
//         ]);

//         // Ambil path file yang diunggah
//         $filePath = $request->file('file')->getPathName();

//         // Load file XLS/XLSX menggunakan PhpSpreadsheet
//         $spreadsheet = IOFactory::load($filePath);

//         // Daftar nama sheet yang ingin dibaca
//         $sheetNames = ['1.2.3', '4.5.6', '7.8.9', '10.11.12', '13.14.15', '16.17'];
//         $dataSummary = [];

//         // Iterasi untuk membaca setiap sheet yang diinginkan
//         foreach ($sheetNames as $sheetName) {
//             $sheet = $spreadsheet->getSheetByName($sheetName);

//             if ($sheet) {
//                 // Baca tanggal (sama untuk semua orang dalam sheet)
//                 $tanggal = $sheet->getCell('D2')->getValue();

//                 // Kolom awal data untuk setiap orang
//                 $startingColumns = [
//                     ['departemen' => 'B', 'nama' => 'J'], // Orang pertama
//                     ['departemen' => 'Q', 'nama' => 'Y'], // Orang kedua
//                     ['departemen' => 'AF', 'nama' => 'AN'], // Orang ketiga
//                 ];

//                 foreach ($startingColumns as $columns) {
//                     // Ambil data berdasarkan lokasi kolom
//                     $departemen = $sheet->getCell($columns['departemen'] . "3")->getValue(); // Departemen
//                     $nama = $sheet->getCell($columns['nama'] . "3")->getValue(); // Nama
//                     $noId = $sheet->getCell($columns['nama'] . "4")->getValue(); // No. ID

//                     // Simpan ke dalam array
//                     $dataSummary[] = [
//                         'sheet' => $sheetName,
//                         'tanggal' => $tanggal,
//                         'departemen' => $departemen,
//                         'nama' => $nama,
//                         'no_id' => $noId,
//                     ];
//                 }
//             }
//         }

//         // Kirim data ke view untuk ditampilkan
//         return view('dashboard.salaries.salary-result', compact('dataSummary'));
//     }
// }

class SalaryController extends Controller
{
    public function uploadForm()
    {
        return view('dashboard.salaries.upload-salary');
    }

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

        // Iterasi untuk membaca setiap sheet yang diinginkan
        foreach ($sheetNames as $sheetName) {
            $sheet = $spreadsheet->getSheetByName($sheetName);

            if ($sheet) {
                // Kolom awal data untuk setiap orang
                $startingColumns = [
                    ['departemen' => 'B', 'nama' => 'J', 'data_awal' => 'B'], // Orang pertama
                    ['departemen' => 'Q', 'nama' => 'Y', 'data_awal' => 'Q'], // Orang kedua
                    ['departemen' => 'AF', 'nama' => 'AN', 'data_awal' => 'AF'], // Orang ketiga
                ];

                foreach ($startingColumns as $columns) {
                    // Ambil data statis berdasarkan lokasi kolom
                    $departemen = $sheet->getCell($columns['departemen'] . "3")->getValue(); // Departemen
                    $nama = $sheet->getCell($columns['nama'] . "3")->getValue(); // Nama
                    $noId = $sheet->getCell($columns['nama'] . "4")->getValue(); // No. ID

                    // Iterasi melalui tanggal (baris 12 ke bawah)
                    $kehadiran = [];
                    for ($row = 12; $row <= 35; $row++) {
                        $tanggal = $sheet->getCell("A{$row}")->getValue(); // Tanggal
                        if ($tanggal) {
                            $col = $columns['data_awal']; // Kolom awal
                            $jamMasuk = $sheet->getCell($col . $row)->getValue(); // Cek jam masuk
                            $jamPulang = null;

                            // Jika ada jam masuk, cek apakah ada jam pulang di kolom sebelah
                            if ($jamMasuk) {
                                $nextCol = chr(ord($col) + 1); // Kolom sebelahnya
                                $jamPulang = $sheet->getCell($nextCol . $row)->getValue();
                            }

                            $kehadiran[] = [
                                'tanggal' => $tanggal,
                                'jam_masuk' => $jamMasuk,
                                'jam_pulang' => $jamPulang,
                            ];
                        }
                    }

                    // Simpan semua data ke dalam array
                    $dataSummary[] = [
                        'sheet' => $sheetName,
                        'departemen' => $departemen,
                        'nama' => $nama,
                        'no_id' => $noId,
                        'kehadiran' => $kehadiran,
                    ];
                }
            }
        }

        // Kirim data ke view untuk ditampilkan
        return view('dashboard.salaries.salary-result', compact('dataSummary'));
    }
}