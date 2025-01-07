<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
                // Baca tanggal (sama untuk semua orang dalam sheet)
                $tanggal = $sheet->getCell('D2')->getValue();

                // Kolom awal data untuk setiap orang
                $startingColumns = [
                    ['departemen' => 'B', 'nama' => 'J'], // Orang pertama
                    ['departemen' => 'Q', 'nama' => 'Y'], // Orang kedua
                    ['departemen' => 'AF', 'nama' => 'AN'], // Orang ketiga
                ];

                foreach ($startingColumns as $columns) {
                    // Ambil data berdasarkan lokasi kolom
                    $departemen = $sheet->getCell($columns['departemen'] . "3")->getValue(); // Departemen
                    $nama = $sheet->getCell($columns['nama'] . "3")->getValue(); // Nama
                    $noId = $sheet->getCell($columns['nama'] . "4")->getValue(); // No. ID

                    // Simpan ke dalam array
                    $dataSummary[] = [
                        'sheet' => $sheetName,
                        'tanggal' => $tanggal,
                        'departemen' => $departemen,
                        'nama' => $nama,
                        'no_id' => $noId,
                    ];
                }
            }
        }

        // Kirim data ke view untuk ditampilkan
        return view('dashboard.salaries.salary-result', compact('dataSummary'));
    }
}
