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
        $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // Kirim data ke view untuk ditampilkan
        return view('dashboard.salaries.salary-result', compact('sheet'));
    }
}
