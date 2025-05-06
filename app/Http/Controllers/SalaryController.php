<?php

namespace App\Http\Controllers;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\JournalDetail;
use App\Models\ChartOfAccount;
use App\Models\TransactionItem;
use App\Models\SalaryCalculation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SalaryController extends Controller
{
    public function slips(Request $request)
    {
        $month = str_pad($request->input('month', now()->subMonth()->format('m')), 2, '0', STR_PAD_LEFT);
        $year = $request->input('year', now()->format('Y'));

        // dd($year.$month);

        $userLogged = Auth::user();
        $roleLogged = optional($userLogged->role)->role_name; // Pastikan tidak error jika role kosong

        // dd($userLogged);

        $bagi_hasil = collect();
        $gaji = null;

        if ($roleLogged == 'admin') {
            // dd('masuk admin');
            $gaji = SalaryCalculation::where('user_id', $userLogged->id)
                ->where('month', "{$year}-{$month}")
                ->orderBy('holiday_shift', 'asc')
                ->select([
                    'id',
                    'user_id',
                    'month',
                    'normal_shift',
                    'holiday_shift',
                    'shift_pagi',
                    'shift_siang',
                    'lembur',
                    'base_salary',
                    'allowance',
                    'grand_total'
                ])
                ->first();

        } else if (stripos($roleLogged, 'dokter') !== false) {
            // dd('masuk dokter');
            $gaji = SalaryCalculation::where('user_id', $userLogged->id)
                ->where('month', "{$year}-{$month}")
                ->orderBy('holiday_shift', 'asc')
                ->select([
                    'id',
                    'user_id',
                    'month',
                    'normal_shift',
                    'holiday_shift',
                    'shift_pagi',
                    'shift_siang',
                    'lembur',
                    'base_salary',
                    'allowance',
                    'grand_total'
                ])
                ->first();

            // dd($gaji);

            // Ambil data bagi hasil
            $bagi_hasil = TransactionItem::join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                ->where('transactions.doctor_id', $userLogged->id)
                ->whereYear('transaction_items.created_at', $year)
                ->whereMonth('transaction_items.created_at', $month)
                ->select([
                    'transaction_items.procedure_id',
                    'transaction_items.quantity',
                    'transaction_items.unit_price',
                    'transaction_items.total_price',
                    'transaction_items.discount',
                    'transaction_items.final_price',
                    'transactions.revenue_percentage',
                    'transactions.revenue_amount',
                ])
                ->get();
        }
        return view('dashboard.salaries.slips', compact('userLogged', 'roleLogged', 'gaji', 'bagi_hasil', 'month', 'year'));
    }

    public function index(Request $request)
    {
        $month = $request->input('month', now()->format('m') - 1);
        $year = $request->input('year', now()->format('Y'));

        $holidays = DB::table('holidays')->pluck('tanggal')->toArray();

        $data = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->join('attendances', function ($join) use ($month, $year) {
                $join->on('users.id', '=', 'attendances.no_id')
                    ->whereMonth('attendances.tanggal', $month)
                    ->whereYear('attendances.tanggal', $year);
            })
            ->select(
                'users.id as no_id',
                'users.name as nama',
                'roles.role_name as role_name',
                DB::raw("COUNT(CASE WHEN NOT EXISTS (SELECT 1 FROM holidays WHERE holidays.tanggal = attendances.tanggal) THEN 1 END) AS normal_shift"),
                DB::raw("COUNT(CASE WHEN EXISTS (SELECT 1 FROM holidays WHERE holidays.tanggal = attendances.tanggal) THEN 1 END) AS holiday_shift")
            )
            ->groupBy('users.id', 'users.name', 'roles.role_name')
            ->get();

        return view('dashboard.salaries.index', compact('data', 'month', 'year'));
    }

    public function calculateSalaries(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        $holidays = DB::table('holidays')->pluck('tanggal')->toArray();

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
                DB::raw("COUNT(CASE WHEN NOT EXISTS (SELECT 1 FROM holidays WHERE holidays.tanggal = attendances.tanggal) THEN 1 END) AS normal_shift"),
                DB::raw("COUNT(CASE WHEN EXISTS (SELECT 1 FROM holidays WHERE holidays.tanggal = attendances.tanggal) THEN 1 END) AS holiday_shift")

            )
            ->groupBy('users.id', 'users.name')
            ->get();

        $salaries = DB::table('users')
            ->join('attendances', function ($join) use ($month, $year) {
                $join->on('users.id', '=', 'attendances.no_id')
                    ->whereMonth('attendances.tanggal', $month)
                    ->whereYear('attendances.tanggal', $year);
            })
            ->where('users.role_id', 1)
            ->select(
                'users.id as user_id',
                'users.name as nama',

                // 1. Cek lembur dulu (lebih dari 12 jam) masuk lembur
                DB::raw("COUNT(CASE 
                WHEN attendances.jam_masuk IS NOT NULL 
                AND attendances.jam_pulang IS NOT NULL 
                AND TIMESTAMPDIFF(MINUTE, attendances.jam_masuk, attendances.jam_pulang) > 720 
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
            // $total_tunjangan = $shift_pagi + $shift_siang + $holiday_shift + $lembur;
            $grand_total = $base_salary + $shift_pagi + $shift_siang + $holiday_shift + $lembur;

            // Simpan hasil perhitungan ke dalam array
            $calculatedSalaries[] = [
                'user_id' => $salary->user_id,
                'nama' => $salary->nama,
                'shift_pagi' => $shift_pagi,
                'shift_siang' => $shift_siang,
                'holiday_shift' => $holiday_shift,
                'lembur' => $lembur,
                'base_salary' => $base_salary,
                'grand_total' => $grand_total
            ];
        }

        $coa = ChartOfAccount::all();

        // dd($calculatedSalaries);

        return view('dashboard.salaries.index', compact('calculatedSalaries', 'month', 'year', 'data', 'coa'));
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

        $bagi_hasil_data = DB::table('transactions')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->groupBy('doctor_id')
            ->select('doctor_id', DB::raw('SUM(revenue_amount) as bagi_hasil'))
            ->pluck('bagi_hasil', 'doctor_id');

        // dd($bagi_hasil_data);

        foreach ($attendances as $attendance) {
            // Hitung jumlah shift berdasarkan total jam kerja dalam sehari
            $jumlah_shift = round($attendance->total_jam_kerja / 8);

            // Tarif transport per shift
            $transport_per_shift = 65000;
            $total_transport = $jumlah_shift * $transport_per_shift;

            // Ambil bagi hasil dokter dari transaksi
            $bagi_hasil = $bagi_hasil_data[$attendance->user_id] ?? 0;

            // Tentukan persentase bagi hasil berdasarkan role_id
            if ($attendance->role_id == 2) {
                // $bagi_hasil = $bagi_hasil * 35 / 100; // Dokter tetap: 35%
                $base_salary = 1500000; // Dokter tetap dapat gaji pokok
            } else {
                // $bagi_hasil = $bagi_hasil * 30 / 100; // Dokter tidak tetap: 30%
                $base_salary = 0; // Dokter tidak tetap tidak dapat gaji pokok
            }

            // Hitung total gaji
            $grand_total = $base_salary + $total_transport + $bagi_hasil;

            // dd($jumlah_shift);

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

        $coa = ChartOfAccount::all();

        return view('dashboard.salaries.index', compact('doctorSalaries', 'month', 'year', 'coa'));
    }

    public function handleSalaries(Request $request)
    {
        if ($request->input('action') == 'calculate') {
            return $this->calculateSalaries($request);
        } elseif ($request->input('action') == 'store') {
            return $this->storeSalaries($request);
        }

        return back()->withErrors(['msg' => 'Aksi tidak valid.']);
    }

    public function storeSalaries(Request $request)
    {
        // dd($request->all()); // Debugging
        $salaries = $request->input('salaries');
        // dd($coa_out);

        if (!is_array($salaries)) {
            return back()->withErrors(['msg' => 'Data gaji tidak valid.']);
        }

        DB::transaction(function () use ($request, $salaries) {
            foreach ($salaries as $salary) {
                SalaryCalculation::updateOrCreate(
                    [
                        'user_id' => $salary['user_id'],
                        'month' => $request->input('year') . '-' . str_pad($request->input('month'), 2, '0', STR_PAD_LEFT)
                    ],
                    [
                        'shift_pagi' => $salary['shift_pagi'] ?? 0,
                        'shift_siang' => $salary['shift_siang'] ?? 0,
                        'holiday_shift' => $salary['holiday_shift'] ?? 0,
                        'lembur' => $salary['lembur'] ?? 0,
                        'base_salary' => $salary['base_salary'] ?? 0,
                        'grand_total' => $salary['grand_total'] + $salary['adjustment'] ?? 0,
                        'adjustment' => $salary['adjustment'] ?? 0,
                        'adjustment_notes' => $salary['adjustment_notes'] ?? null,
                    ]
                );

                // // Simpan Journal Entry (langsung masuk ke Beban Gaji)
                $journal = \App\Models\JournalEntry::create([
                    'entry_date' => now(),
                    'description' => "Pembayaran Gaji " . $request->input('year') . '/' . str_pad($request->input('month'), 2, '0', STR_PAD_LEFT) . " untuk user ID: " . $salary['user_id'],
                ]);

                $idBebanGaji = ChartOfAccount::where('name', 'Beban Gaji')->value('id');
                // $idKas = ChartOfAccount::where('name', 'Kas')->value('id');

                // // Simpan Journal Details (Debit - Beban Gaji)
                JournalDetail::create([
                    'journal_entry_id' => $journal->id,
                    'coa_id' => $idBebanGaji,
                    'debit' => ($salary['grand_total'] ?? 0) + ($salary['adjustment'] ?? 0),
                    'credit' => 0,
                ]);

                // // Simpan Journal Details (Kredit - Kas/Bank)
                JournalDetail::create([
                    'journal_entry_id' => $journal->id,
                    'coa_id' => $request->input('coa_out'),
                    'debit' => 0,
                    'credit' => ($salary['grand_total'] ?? 0) + ($salary['adjustment'] ?? 0),
                ]);
            }
        });

        return redirect()->route('dashboard.salaries.index')->with('success', 'Salaries and Journal Created');
    }

    public function storeDoctorSalaries(Request $request)
    {
        $salaries = $request->input('doctorSalaries');

        if (!is_array($salaries)) {
            return back()->withErrors(['msg' => 'Data gaji tidak valid.']);
        }

        DB::transaction(function () use ($request, $salaries) {
            foreach ($salaries as $salary) {
                // Hitung grand total setelah adjustment
                $grandTotalFinal = ($salary['grand_total'] ?? 0) + ($salary['adjustment'] ?? 0);

                // Simpan atau update data gaji dokter
                $salaryRecord = SalaryCalculation::updateOrCreate(
                    [
                        'user_id' => $salary['user_id'],
                        'month' => $request->input('year') . '-' . str_pad($request->input('month'), 2, '0', STR_PAD_LEFT)
                    ],
                    [
                        'shift_pagi' => $salary['bagi_hasil'],
                        'shift_siang' => 0,
                        'holiday_shift' => 0,
                        'lembur' => 0,
                        'base_salary' => $salary['base_salary'],
                        'allowance' => $salary['transport_total'],
                        'grand_total' => $grandTotalFinal + $salary['bagi_hasil'],
                        'adjustment' => $salary['adjustment'] ?? 0,
                        'adjustment_notes' => $salary['adjustment_notes'] ?? null,
                    ]
                );

                // Simpan Journal Entry (langsung masuk ke Beban Gaji Dokter)
                $journal = \App\Models\JournalEntry::create([
                    'entry_date' => now(),
                    'description' => "Pembayaran Gaji Dokter bulan " . $salaryRecord->month . " untuk user ID: " . $salary['user_id'],
                ]);

                $idBebanGaji = ChartOfAccount::where('name', 'Beban Gaji')->value('id');
                $idBagiHasil = ChartOfAccount::where('name', 'Bagi Hasil Dokter')->value('id');
                // $idKas = ChartOfAccount::where('name', 'Kas')->value('id');

                // Simpan Journal Details (Debit - Beban Gaji)
                JournalDetail::create([
                    'journal_entry_id' => $journal->id,
                    'coa_id' => $idBebanGaji, // ID akun Beban Gaji
                    'debit' => $grandTotalFinal,
                    'credit' => 0,
                ]);

                JournalDetail::create([
                    'journal_entry_id' => $journal->id,
                    'coa_id' => $idBagiHasil, // ID akun Beban bagi hasil
                    'debit' => $salary['bagi_hasil'],
                    'credit' => 0,
                ]);

                // Simpan Journal Details (Kredit - Kas/Bank)
                JournalDetail::create([
                    'journal_entry_id' => $journal->id,
                    'coa_id' => $request->input('coa_out'), // ID akun Kas/Bank
                    'debit' => 0,
                    'credit' => $grandTotalFinal,
                ]);
            }
        });

        return redirect()->route('dashboard.salaries.index')->with('success', 'Salaries and Journals Created');
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
                // $dataSummary[] = [
                //     'sheet' => $sheetName,
                //     'departemen' => $departemen,
                //     'nama' => $nama,
                //     'no_id' => $noId,
                //     'kehadiran' => $kehadiran,
                // ];
            }
        }

        // Kirim data ke view untuk ditampilkan
        return redirect()->route('dashboard.salaries.index')->with('success', 'Salaries Created');
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
            ->with('success', 'Attendances Created');
    }

    public function edit($id)
    {
        $attendance = Attendance::findOrFail($id);
        return view('dashboard.attendances.edit', compact('attendance'));
    }

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
            ->with('success', 'Attendances Updated');
    }

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return redirect()->route('attendances.index')
            ->with('success', 'Attendances Deleted');
    }
}
