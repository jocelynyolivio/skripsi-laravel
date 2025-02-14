<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\SalaryCalculation;
use Dotenv\Util\Regex;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->format('m') - 1);
        $year = $request->input('year', now()->format('Y'));

        // Ambil daftar hari libur dari database
        $holidays = DB::table('holidays')->pluck('tanggal')->toArray();

        // Ambil data absensi
        $data = DB::table('users')
            ->join('attendances', function ($join) use ($month, $year) {
                $join->on('users.id', '=', 'attendances.no_id')
                    ->whereMonth('attendances.tanggal', $month)
                    ->whereYear('attendances.tanggal', $year);
            })
            ->select(
                'users.id as no_id',
                'users.name as nama',
                DB::raw("COUNT(CASE WHEN attendances.tanggal NOT IN ('" . implode("','", $holidays) . "') THEN 1 END) AS normal_shift"),
                DB::raw("COUNT(CASE WHEN attendances.tanggal IN ('" . implode("','", $holidays) . "') THEN 1 END) AS holiday_shift")
            )
            ->groupBy('users.id', 'users.name')
            ->get();


        return view('dashboard.salaries.index', compact('data', 'month', 'year'));
    }

    public function calculateSalaries(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        // Ambil daftar hari libur dari database
        $holidays = DB::table('holidays')->pluck('tanggal')->toArray();

        // Ambil data absensi
        $data = DB::table('users')
            ->join('attendances', function ($join) use ($month, $year) {
                $join->on('users.id', '=', 'attendances.no_id')
                    ->whereMonth('attendances.tanggal', $month)
                    ->whereYear('attendances.tanggal', $year);
            })
            ->where('users.role_id', 1)
            ->select(
                'users.id as no_id',
                'users.name as nama',
                DB::raw("COUNT(CASE WHEN attendances.tanggal NOT IN ('" . implode("','", $holidays) . "') THEN 1 END) AS normal_shift"),
                DB::raw("COUNT(CASE WHEN attendances.tanggal IN ('" . implode("','", $holidays) . "') THEN 1 END) AS holiday_shift")
            )
            ->groupBy('users.id', 'users.name')
            ->get();
        

        // Ambil data absensi dan hitung gaji
        $salaries = DB::table('users')
            ->join('attendances', function ($join) use ($month, $year) {
                $join->on('users.id', '=', 'attendances.no_id')
                    ->whereMonth('attendances.tanggal', $month)
                    ->whereYear('attendances.tanggal', $year);
            })
            ->where('users.role_id', 1) // Hanya admin
            ->select(
                'users.id as user_id',
                'users.name as nama',

                // 1. Cek lembur dulu (lebih dari 12 jam) masuk lembur
                DB::raw("COUNT(CASE 
                        WHEN TIMESTAMPDIFF(MINUTE, attendances.jam_masuk, attendances.jam_pulang) > 720 
                        THEN 1 
                    END) AS lembur"),

                // 2. Jika tidak lembur, cek apakah masuk hari libur masuk holiday
                DB::raw("COUNT(CASE 
                        WHEN TIMESTAMPDIFF(MINUTE, attendances.jam_masuk, attendances.jam_pulang) <= 720 
                        AND attendances.tanggal IN ('" . implode("','", $holidays) . "') 
                        THEN 1 
                    END) AS holiday_shift"),

                // 3. Jika tidak lembur dan tidak hari libur, baru cek shift pagi
                DB::raw("COUNT(CASE 
                        WHEN TIMESTAMPDIFF(MINUTE, attendances.jam_masuk, attendances.jam_pulang) <= 720 
                        AND attendances.tanggal NOT IN ('" . implode("','", $holidays) . "') 
                        AND TIME(attendances.jam_masuk) BETWEEN '06:00:00' AND '10:00:00' 
                        THEN 1 
                    END) AS shift_pagi"),

                // 4. Jika tidak lembur dan tidak hari libur, baru cek shift siang
                DB::raw("COUNT(CASE 
                        WHEN TIMESTAMPDIFF(MINUTE, attendances.jam_masuk, attendances.jam_pulang) <= 720 
                        AND attendances.tanggal NOT IN ('" . implode("','", $holidays) . "') 
                        AND TIME(attendances.jam_masuk) BETWEEN '10:01:00' AND '14:00:00' 
                        THEN 1 
                    END) AS shift_siang")
            )
            ->groupBy('users.id', 'users.name')
            ->get();

        // dd($salaries);

        $calculatedSalaries = [];

        foreach ($salaries as $salary) {
            $base_salary = 600000; // Gaji Pokok

            // Shift pagi & shift siang dikalikan 40.000 (normal shift)
            $shift_pagi = $salary->shift_pagi * 40_000;
            $shift_siang = $salary->shift_siang * 40_000;

            // Holiday shift dan lembur sama-sama dikalikan 80.000
            $holiday_shift = $salary->holiday_shift * 80_000;
            $lembur = $salary->lembur * 80_000;

            // Total tunjangan dihitung dari semua kategori
            $total_tunjangan = $shift_pagi + $shift_siang + $holiday_shift + $lembur;
            $grand_total = $base_salary + $total_tunjangan;

            // Simpan hasil perhitungan ke dalam array
            $calculatedSalaries[] = [
                'user_id' => $salary->user_id,
                'nama' => $salary->nama,
                'shift_pagi' => $shift_pagi,
                'shift_siang' => $shift_siang,
                'holiday_shift' => $holiday_shift,
                'lembur' => $lembur,
                'base_salary' => $base_salary,
                'allowance' => $total_tunjangan,
                'grand_total' => $grand_total
            ];
        }

        // dd($calculatedSalaries);

        return view('dashboard.salaries.index', compact('calculatedSalaries', 'month', 'year', 'data'));
    }
    public function calculateDoctorSalaries(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
    
        // Ambil data absensi dokter tetap (role_id = 2) dan tidak tetap (role_id = 3)
        $attendances = DB::table('users')
            ->join('attendances', function ($join) use ($month, $year) {
                $join->on('users.id', '=', 'attendances.no_id')
                    ->whereMonth('attendances.tanggal', $month)
                    ->whereYear('attendances.tanggal', $year);
            })
            ->whereIn('users.role_id', [2, 3]) // Role ID 2 = Dokter Tetap, Role ID 3 = Dokter Tidak Tetap
            ->select(
                'users.id as user_id',
                'users.name as nama',
                'users.role_id',
                DB::raw('COUNT(attendances.tanggal) as jumlah_kehadiran'), // Jumlah hari hadir
                DB::raw('SUM(TIMESTAMPDIFF(MINUTE, attendances.jam_masuk, attendances.jam_pulang))/60 as total_jam_kerja') // Total jam kerja dalam jam
            )
            ->groupBy('users.id', 'users.name', 'users.role_id')
            ->get();
    
        $doctorSalaries = [];
    
        // Ambil total amount dari transactions berdasarkan dokter & bulan yang dipilih
        $bagi_hasil_data = DB::table('transactions')
        ->join('medical_records', 'transactions.medical_record_id', '=', 'medical_records.id') // Ambil reservation_id dari medical_records
        ->join('reservations', 'medical_records.reservation_id', '=', 'reservations.id') // Ambil doctor_id dari reservations
        ->whereMonth('reservations.tanggal_reservasi', $month) // Filter berdasarkan bulan
        ->whereYear('reservations.tanggal_reservasi', $year) // Filter berdasarkan tahun
        ->groupBy('reservations.doctor_id')
        ->select('reservations.doctor_id', DB::raw('SUM(transactions.amount) as bagi_hasil'))
        ->pluck('bagi_hasil', 'doctor_id'); // Ubah hasil menjadi array dengan doctor_id sebagai key
    
    
        foreach ($attendances as $attendance) {
            // Hitung jumlah shift berdasarkan total jam kerja dalam sehari
            $jumlah_shift = round($attendance->total_jam_kerja / 8); // Misal kerja 16 jam = 2 shift
    
            // Tarif transport per shift
            $transport_per_shift = 65000;
            $total_transport = $jumlah_shift * $transport_per_shift;
    
            // Ambil bagi hasil dokter dari transaksi
            $bagi_hasil = $bagi_hasil_data[$attendance->user_id] ?? 0;
    
            // Tentukan persentase bagi hasil berdasarkan role_id
            if ($attendance->role_id == 2) {
                $bagi_hasil = $bagi_hasil * 35 / 100; // Dokter tetap: 35%
                $base_salary = 1500000; // Dokter tetap dapat gaji pokok
            } else {
                $bagi_hasil = $bagi_hasil * 30 / 100; // Dokter tidak tetap: 30%
                $base_salary = 0; // Dokter tidak tetap tidak dapat gaji pokok
            }
    
            // Hitung total gaji
            $grand_total = $base_salary + $total_transport + $bagi_hasil;
    
            // Simpan hasil perhitungan dalam array
            $doctorSalaries[] = [
                'user_id' => $attendance->user_id,
                'nama' => $attendance->nama,
                'role_id' => $attendance->role_id,
                'jumlah_kehadiran' => $attendance->jumlah_kehadiran,
                'shift_count' => $jumlah_shift,
                'transport_total' => $total_transport,
                'bagi_hasil' => $bagi_hasil,
                'base_salary' => $base_salary,
                'grand_total' => $grand_total
            ];
        }
    
        return view('dashboard.salaries.index', compact('doctorSalaries', 'month', 'year'));
    }
    
    
    


    public function storeSalaries(Request $request)
    {
        // Ubah dari JSON ke array
        $salaries = json_decode($request->input('salaries'), true);

        // Pastikan data berhasil di-decode
        if (!is_array($salaries)) {
            return back()->withErrors(['msg' => 'Data gaji tidak valid.']);
        }

        foreach ($salaries as $salary) {
            SalaryCalculation::updateOrCreate(
                [
                    'user_id' => $salary['user_id'],
                    'month' => $request->input('year') . '-' . $request->input('month')
                ],
                [
                    'shift_pagi' => $salary['shift_pagi'],
                    'shift_siang' => $salary['shift_siang'],
                    'lembur' => $salary['lembur'],
                    'base_salary' => $salary['base_salary'],
                    'allowance' => $salary['allowance'],
                    'grand_total' => $salary['grand_total']
                ]
            );
        }

        return redirect()->route('dashboard.salaries.index')->with('success', 'Gaji berhasil disimpan!');
    }

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
        $sheetNames = ['1.2.3', '4.5.6', '7.8.9', '10.11.12', '13.14.15', '16.17.18',];
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

    /**
     * Simpan data baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_id' => 'required|integer',
            'nama' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jam_masuk' => 'nullable|date_format:H:i',
            'jam_pulang' => 'nullable|date_format:H:i',
        ]);

        Attendance::create($request->all());

        return redirect()->route('attendances.index')
                         ->with('success', 'Data absensi berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit absensi.
     */
    public function edit($id)
    {
        $attendance = Attendance::findOrFail($id);
        return view('dashboard.attendances.edit', compact('attendance'));
    }

    /**
     * Update data absensi di database.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'no_id' => 'required|integer',
            'nama' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jam_masuk' => 'nullable|date_format:H:i',
            'jam_pulang' => 'nullable|date_format:H:i',
        ]);

        $attendance = Attendance::findOrFail($id);
        $attendance->update($request->all());

        return redirect()->route('attendances.index')
                         ->with('success', 'Data absensi berhasil diperbarui.');
    }

    /**
     * Hapus data absensi.
     */
    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return redirect()->route('attendances.index')
                         ->with('success', 'Data absensi berhasil dihapus.');
    }

    // public function processSalaries(Request $request)
    // {
    //     $month = $request->input('month', now()->format('m'));
    //     $year = $request->input('year', now()->format('Y'));

    //     // Ambil daftar hari libur nasional dari database
    //     $holidays = DB::table('holidays')->pluck('tanggal')->toArray();

    //     // Ambil data absensi dan hitung jumlah shift per user
    //     $salaries = DB::table('users')
    //         ->leftJoin('attendances', function ($join) use ($month, $year) {
    //             $join->on('users.id', '=', 'attendances.no_id')
    //                 ->whereMonth('attendances.tanggal', $month)
    //                 ->whereYear('attendances.tanggal', $year);
    //         })
    //         ->select(
    //             'users.id as user_id',
    //             'users.name as nama',
    //             'users.role_id',
    //             'attendances.jam_masuk',
    //             'attendances.jam_pulang',
    //             'attendances.tanggal'
    //         )
    //         ->get();
    //     $salaryData = [];

    //     // loop
    //     foreach ($salaries as $salary) {
    //         $jam_masuk = \Carbon\Carbon::parse($salary->jam_masuk);
    //         $jam_pulang = \Carbon\Carbon::parse($salary->jam_pulang);
    //         $total_jam_kerja = $jam_pulang->diffInHours($jam_masuk);

    //         // Default tarif
    //         $normal_rate = 40_000; // Rp 40.000 per jam
    //         $lembur_rate = 80_000; // Rp 80.000 per jam
    //         $base_salary = 600_000; // Gaji Pokok

    //         $shift_pagi = 0;
    //         $shift_siang = 0;
    //         $lembur = 0;

    //         if ($salary->role_id == 1) { // Admin

    //             // 1️⃣ Cek apakah masuk kategori LEMBUR terlebih dahulu
    //             if ($total_jam_kerja > 12 || in_array($salary->tanggal, $holidays)) {
    //                 $lembur += $total_jam_kerja * $lembur_rate;
    //             }
    //             // 2️⃣ Jika tidak lembur, baru cek apakah Shift Pagi atau Shift Siang
    //             else {
    //                 if ($jam_masuk->hour < 8) {
    //                     $shift_pagi += $total_jam_kerja * $normal_rate;
    //                 } elseif ($jam_masuk->hour > 12) {
    //                     $shift_siang += $total_jam_kerja * $normal_rate;
    //                 }
    //             }
    //         }

    //         $total_tunjangan = $shift_pagi + $shift_siang + $lembur;
    //         $grand_total = $base_salary + $total_tunjangan;

    //         $salaryData[] = [
    //             'user_id' => $salary->user_id,
    //             'month' => "$year-$month",
    //             'shift_pagi' => $shift_pagi,
    //             'shift_siang' => $shift_siang,
    //             'lembur' => $lembur,
    //             'base_salary' => $base_salary,
    //             'allowance' => $total_tunjangan,
    //             'grand_total' => $grand_total
    //         ];
    //     }


    //     // Simpan hasil ke database
    //     foreach ($salaryData as $data) {
    //         SalaryCalculation::updateOrCreate(
    //             ['user_id' => $data['user_id'], 'month' => $data['month']],
    //             $data
    //         );
    //     }

    //     return redirect()->route('dashboard.salaries.index')->with('success', 'Gaji berhasil dihitung dan disimpan!');
    // }

    // public function index(Request $request)
    // {
    //     // Ambil parameter bulan & tahun dari request (default ke bulan ini)
    //     $month = $request->input('month', now()->format('m'));
    //     $year = $request->input('year', now()->format('Y'));

    //     // Ambil daftar hari libur nasional dari database tanpa model
    //     $holidays = DB::table('holidays')->pluck('tanggal')->toArray();

    //     // Hitung jumlah shift normal dan holiday shift per no_id dengan filter bulan & tahun
    //     $data = DB::table('attendances')->join('users', 'attendances.no_id', '=', 'users.id') // Join users untuk mendapatkan nama

    //         ->select(
    //             'attendances.no_id',
    //             'users.name as nama', // Ambil nama dari users
    //             DB::raw("COUNT(CASE WHEN tanggal NOT IN ('" . implode("','", $holidays) . "') THEN 1 END) AS normal_shift"),
    //             DB::raw("COUNT(CASE WHEN tanggal IN ('" . implode("','", $holidays) . "') THEN 1 END) AS holiday_shift")
    //         )
    //         ->whereMonth('tanggal', $month) // Filter bulan
    //         ->whereYear('tanggal', $year)   // Filter tahun
    //         ->groupBy('attendances.no_id', 'users.name')
    //         ->get();

    //     return view('dashboard.salaries.index', compact('data', 'month', 'year'));
    // }

    // sebelum calculation
    // public function index(Request $request)
    // {
    //     // Ambil parameter bulan & tahun dari request (default ke bulan ini)
    //     $month = $request->input('month', now()->format('m'));
    //     $year = $request->input('year', now()->format('Y'));

    //     // Ambil daftar hari libur dari database
    //     $holidays = DB::table('holidays')->pluck('tanggal')->toArray();

    //     // Query untuk mengambil semua users + LEFT JOIN ke attendances
    //     $data = DB::table('users')
    //         ->leftJoin('attendances', function ($join) use ($month, $year) {
    //             $join->on('users.id', '=', 'attendances.no_id')
    //                 ->whereMonth('attendances.tanggal', $month)
    //                 ->whereYear('attendances.tanggal', $year);
    //         })
    //         ->select(
    //             'users.id as no_id',
    //             'users.name as nama', // Ambil nama dari users
    //             DB::raw("COALESCE(COUNT(CASE WHEN attendances.tanggal NOT IN ('" . implode("','", $holidays) . "') THEN 1 END), 0) AS normal_shift"),
    //             DB::raw("COALESCE(COUNT(CASE WHEN attendances.tanggal IN ('" . implode("','", $holidays) . "') THEN 1 END), 0) AS holiday_shift")
    //         )
    //         ->groupBy('users.id', 'users.name') // Kelompokkan berdasarkan ID dan Nama
    //         ->get();

    //     return view('dashboard.salaries.index', compact('data', 'month', 'year'));
    // }

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

}
