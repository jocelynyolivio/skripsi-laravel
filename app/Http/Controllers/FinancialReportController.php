<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\JournalDetail;

class FinancialReportController extends Controller
{
    // ini show semua berdasarkan coa kabeh
    // public function balanceSheet(Request $request)
    // {
    //     $period = $request->get('period', 'monthly'); // Default bulanan
    //     $date = $request->get('date', Carbon::now()->format('Y-m-d'));

    //     $query = JournalDetail::selectRaw('chart_of_accounts.name, SUM(debit) as total_debit, SUM(credit) as total_credit')
    //         ->join('journal_entries', 'journal_details.journal_entry_id', '=', 'journal_entries.id')
    //         ->join('chart_of_accounts', 'journal_details.coa_id' , '=' , 'chart_of_accounts.id');

    //     if ($period == 'monthly') {
    //         $query->whereMonth('journal_entries.entry_date', Carbon::parse($date)->month)
    //               ->whereYear('journal_entries.entry_date', Carbon::parse($date)->year);
    //     } elseif ($period == 'yearly') {
    //         $query->whereYear('journal_entries.entry_date', Carbon::parse($date)->year);
    //     }

    //     $coaSummary = $query->groupBy('chart_of_accounts.name')->get();

    //     return view('dashboard.reports.balance_sheet', compact('coaSummary', 'period', 'date'));
    // }
public function balanceSheet(Request $request)
{
    $periodForDisplay = $request->get('period', 'monthly'); // Digunakan untuk display atau jika ada logika P/L spesifik periode
    $dateInput = $request->get('date', Carbon::now()->format('Y-m-d'));
    $parsedDate = Carbon::parse($dateInput);

    try {
        // Query untuk saldo akun Neraca (Aset, Liabilitas, Ekuitas)
        // Harus mengambil semua transaksi s/d tanggal laporan ($parsedDate)
        $coaBalancesQuery = JournalDetail::selectRaw(
            'chart_of_accounts.id as coa_id,
             chart_of_accounts.name as coa_name,
             chart_of_accounts.code as coa_code,  // Tambahkan coa_code jika diperlukan di view
             chart_of_accounts.type,
             SUM(journal_details.debit) as total_debit,
             SUM(journal_details.credit) as total_credit'
        )
        ->join('journal_entries', 'journal_details.journal_entry_id', '=', 'journal_entries.id')
        ->join('chart_of_accounts', 'journal_details.coa_id', '=', 'chart_of_accounts.id')
        ->whereIn('chart_of_accounts.type', ['asset', 'liability', 'equity']) // Hanya akun Neraca
        ->whereDate('journal_entries.entry_date', '<=', $parsedDate->toDateString()); // KRITIKAL: Hingga tanggal laporan

        // Jika Anda memiliki saldo awal akun (opening balances) yang disimpan terpisah dan tidak melalui jurnal,
        // Anda perlu menggabungkannya di sini. Untuk saat ini, kita asumsikan semua dari jurnal.

        $coaSummary = $coaBalancesQuery->groupBy('chart_of_accounts.id', 'chart_of_accounts.name', 'chart_of_accounts.code', 'chart_of_accounts.type')
            ->orderBy('chart_of_accounts.code') // Urutkan berdasarkan kode akun
            ->get();

        // Penanganan Akun Kontra (Contoh: Akumulasi Penyusutan)
        // Akun kontra-aset seperti Akumulasi Penyusutan biasanya memiliki saldo normal kredit.
        // Perhitungan saldo aset (debit - kredit) akan secara otomatis menguranginya.
        // Jadi, logika swap manual seperti untuk "Diskon Pembelian" sebaiknya dihindari.
        // Jika "Diskon Pembelian" adalah kontra-aset (yang jarang), pastikan tipe CoA-nya benar
        // dan saldo normalnya (kredit) akan mengurangi total aset saat `debit - kredit` dihitung.
        // Saya akan menghapus logika swap untuk 'Diskon Pembelian' karena berpotensi salah
        // dan menyarankan untuk memperbaiki klasifikasi CoA jika akun tsb tidak sesuai.
        // Jika 'Diskon Pembelian' seharusnya mempengaruhi HPP, itu akan masuk ke perhitungan Laba/Rugi.

        // Perhitungan Laba/Rugi Kumulatif s/d Tanggal Laporan
        // Ini adalah laba/rugi yang akan menjadi bagian dari ekuitas (Laba Ditahan)
        $cumulativeProfitOrLossQuery = JournalDetail::selectRaw(
            '(SUM(CASE WHEN chart_of_accounts.type = \'revenue\' THEN journal_details.credit ELSE 0 END) -
              SUM(CASE WHEN chart_of_accounts.type = \'revenue\' THEN journal_details.debit ELSE 0 END)) -
             (SUM(CASE WHEN chart_of_accounts.type IN (\'expense\', \'cogs\') THEN journal_details.debit ELSE 0 END) -
              SUM(CASE WHEN chart_of_accounts.type IN (\'expense\', \'cogs\') THEN journal_details.credit ELSE 0 END))
             as net_income'
        )
        ->join('journal_entries', 'journal_details.journal_entry_id', '=', 'journal_entries.id')
        ->join('chart_of_accounts', 'journal_details.coa_id', '=', 'chart_of_accounts.id')
        // Asumsi 'contra_revenue' juga sudah diperhitungkan menjadi pengurang revenue di atas
        // Jika 'contra_revenue' punya type sendiri, perlu penyesuaian di query net_income
        ->whereIn('chart_of_accounts.type', ['revenue', 'contra_revenue', 'expense', 'cogs']) // Sesuaikan dengan tipe akun L/R Anda
        ->whereDate('journal_entries.entry_date', '<=', $parsedDate->toDateString()); // Kumulatif s/d tanggal laporan

        // Jika Anda ingin Laba/Rugi untuk PERIODE tertentu (misalnya hanya bulan/tahun ini) untuk ditampilkan
        // terpisah dari Laba Ditahan Awal, Anda bisa buat query tambahan dengan filter periode ($periodForDisplay)
        // Namun untuk total ekuitas di Neraca, yang dibutuhkan adalah laba/rugi kumulatif.

        $profitOrLossResult = $cumulativeProfitOrLossQuery->first();
        $netIncomeForEquity = $profitOrLossResult ? $profitOrLossResult->net_income : 0;

        // Cek apakah sudah ada akun Laba Ditahan (misalnya dari proses tutup buku sebelumnya)
        $existingRetainedEarnings = $coaSummary->firstWhere('coa_name', 'Laba Ditahan'); // Atau berdasarkan kode akun Laba Ditahan

        if ($existingRetainedEarnings) {
            // Jika ada, tambahkan net_income ke saldo Laba Ditahan yang ada
            // Ini asumsi net_income dihitung dari awal periode fiskal atau sejak tutup buku terakhir
            // Untuk kesederhanaan, kita update saldonya langsung.
            // Dalam sistem nyata, ini bisa lebih kompleks (saldo awal + P/L periode berjalan)
            if ($netIncomeForEquity > 0) {
                $existingRetainedEarnings->total_credit += $netIncomeForEquity;
            } else {
                $existingRetainedEarnings->total_debit += abs($netIncomeForEquity);
            }
        } else {
            // Jika tidak ada akun Laba Ditahan, tambahkan sebagai item baru
            // Ini menyiratkan $netIncomeForEquity adalah akumulasi laba/rugi sejak awal
            if ($netIncomeForEquity != 0) { // Hanya tambahkan jika ada laba/rugi
                 $coaSummary->push((object)[
                    'coa_id' => 'RE', // Beri ID unik sementara atau null
                    'coa_code' => '3-XXXX', // Beri kode akun sementara
                    'coa_name' => 'Akumulasi Laba/Rugi (Laba Ditahan)',
                    'type' => 'equity',
                    'total_debit' => ($netIncomeForEquity < 0) ? abs($netIncomeForEquity) : 0,
                    'total_credit' => ($netIncomeForEquity > 0) ? $netIncomeForEquity : 0,
                ]);
            }
        }

        $assets = $coaSummary->where('type', 'asset')->values(); // Gunakan values() untuk reindex array
        $liabilities = $coaSummary->where('type', 'liability')->values();
        $equities = $coaSummary->where('type', 'equity')->values();

        // Hitung Saldo Akhir untuk setiap Akun dan Total
        $totalAssets = 0;
        foreach ($assets as $item) {
            // Akun kontra-aset (mis. Akumulasi Penyusutan) normalnya kredit
            $item->balance = $item->total_debit - $item->total_credit;
            $totalAssets += $item->balance;
        }

        $totalLiabilities = 0;
        foreach ($liabilities as $item) {
            $item->balance = $item->total_credit - $item->total_debit;
            $totalLiabilities += $item->balance;
        }

        $totalEquities = 0;
        foreach ($equities as $item) {
            $item->balance = $item->total_credit - $item->total_debit;
            $totalEquities += $item->balance;
        }
        
        return view('dashboard.reports.balance_sheet', compact(
            'periodForDisplay', // Untuk judul atau filter di view jika masih dipakai
            'dateInput',        // Untuk menampilkan tanggal laporan di view
            'assets',
            'liabilities',
            'equities',
            'totalAssets',
            'totalLiabilities',
            'totalEquities'
        ));

    } catch (\Exception $e) {
        // \Log::error('Error generating balance sheet: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        return redirect()->back()->with('error', 'Gagal memuat laporan neraca: ' . $e->getMessage());
    }
}
    // public function balanceSheet(Request $request)
    // {
    //     $period = $request->get('period', 'monthly'); // Default bulanan
    //     $date = $request->get('date', Carbon::now()->format('Y-m-d'));

    //     $query = JournalDetail::selectRaw(
    //         '
    //         chart_of_accounts.id as coa_id, 
    //         chart_of_accounts.name as coa_name, 
    //         chart_of_accounts.type, 
    //         SUM(journal_details.debit) as total_debit, 
    //         SUM(journal_details.credit) as total_credit'
    //     )
    //         ->join('journal_entries', 'journal_details.journal_entry_id', '=', 'journal_entries.id')
    //         ->join('chart_of_accounts', 'journal_details.coa_id', '=', 'chart_of_accounts.id')
    //         ->whereIn('chart_of_accounts.type', ['asset', 'liability', 'equity']); // Hanya akun Neraca

    //     if ($period == 'monthly') {
    //         $query->whereMonth('journal_entries.entry_date', Carbon::parse($date)->month)
    //             ->whereYear('journal_entries.entry_date', Carbon::parse($date)->year);
    //     } elseif ($period == 'yearly') {
    //         $query->whereYear('journal_entries.entry_date', Carbon::parse($date)->year);
    //     }

    //     $coaSummary = $query->groupBy('chart_of_accounts.id', 'chart_of_accounts.name', 'chart_of_accounts.type')
    //         ->get();

    //     // Balik posisi akun Diskon Pembelian (akun contra)
    //     foreach ($coaSummary as $coa) {
    //         if (str_contains(strtolower($coa->coa_name), 'diskon pembelian')) {
    //             $temp = $coa->total_debit;
    //             $coa->total_debit = $coa->total_credit;
    //             $coa->total_credit = $temp;
    //         }
    //     }


    //     // ini ngitung laba ditahan (retained earnings)
    //     // kalau laba rugi nya menunjukan rugi jadi laba ditahannya bertambah di debit (supaya nanti balance)
    //     $profitOrLoss = JournalDetail::selectRaw("
    //     SUM(journal_details.credit) - SUM(journal_details.debit) as net_income
    // ")
    //         ->join('journal_entries', 'journal_details.journal_entry_id', '=', 'journal_entries.id')
    //         ->join('chart_of_accounts', 'journal_details.coa_id', '=', 'chart_of_accounts.id')
    //         ->whereIn('chart_of_accounts.type', ['revenue', 'expense']) // Hanya akun laba rugi
    //         ->whereDate('journal_entries.entry_date', '<=', $date)
    //         ->first();

    //     // Jika ada laba/rugi, tambahkan ke laporan neraca
    //     if ($profitOrLoss) {
    //         $netIncome = $profitOrLoss->net_income;

    //         // Jika laba positif, masuk ke Kredit di ekuitas
    //         // Jika rugi, masuk ke Debit di ekuitas
    //         // harusnya nanti disini kalo untung masuk sebagai nett incomee
    //         $coaSummary->push((object)[
    //             'coa_id' => null,
    //             'coa_name' => 'Laba Ditahan (Setelah Laba/Rugi)',
    //             'type' => 'equity',
    //             'total_debit' => ($netIncome < 0) ? abs($netIncome) : 0,
    //             'total_credit' => ($netIncome > 0) ? $netIncome : 0,
    //         ]);
    //     }

    //     //     return view('dashboard.reports.balance_sheet', compact('coaSummary', 'period', 'date'));
    //     $assets = $coaSummary->where('type', 'asset');
    //     $liabilities = $coaSummary->where('type', 'liability');
    //     $equities = $coaSummary->where('type', 'equity');

    //     // Hitung subtotal
    //     $totalAssets = $assets->sum(function ($item) {
    //         return $item->total_debit - $item->total_credit;
    //     });

    //     $totalLiabilities = $liabilities->sum(function ($item) {
    //         return $item->total_credit - $item->total_debit;
    //     });

    //     $totalEquities = $equities->sum(function ($item) {
    //         return $item->total_credit - $item->total_debit;
    //     });
    //     // dd($totalEquities);

    //     return view('dashboard.reports.balance_sheet', compact(
    //         'period',
    //         'date',
    //         'assets',
    //         'liabilities',
    //         'equities',
    //         'totalAssets',
    //         'totalLiabilities',
    //         'totalEquities'
    //     ));
    // }

    // public function incomeStatement(Request $request)
    // {
    //     $period = $request->get('period', 'monthly');
    //     $date = $request->get('date', Carbon::now()->format('Y-m-d'));

    //     $query = JournalDetail::join('journal_entries', 'journal_details.journal_entry_id', '=', 'journal_entries.id')
    //         ->join('chart_of_accounts', 'journal_details.coa_id', '=', 'chart_of_accounts.id');

    //     if ($period == 'monthly') {
    //         $query->whereMonth('journal_entries.entry_date', Carbon::parse($date)->month)
    //             ->whereYear('journal_entries.entry_date', Carbon::parse($date)->year);
    //     } elseif ($period == 'yearly') {
    //         $query->whereYear('journal_entries.entry_date', Carbon::parse($date)->year);
    //     }

    //     $revenues = (clone $query)->where('chart_of_accounts.type', 'revenue')
    //         ->selectRaw('chart_of_accounts.name, SUM(credit) as saldo')
    //         ->groupBy('chart_of_accounts.name')
    //         ->get();

    //     $hpp = (clone $query)->where('chart_of_accounts.name', 'LIKE', '%HPP%')
    //         ->selectRaw('chart_of_accounts.name, SUM(debit) as saldo')
    //         ->groupBy('chart_of_accounts.name')
    //         ->get();

    //     $operatingExpenses = (clone $query)->where('chart_of_accounts.type', 'expense')
    //         ->where('chart_of_accounts.name', 'NOT LIKE', '%HPP%')
    //         ->selectRaw('chart_of_accounts.name, SUM(debit) as saldo')
    //         ->groupBy('chart_of_accounts.name')
    //         ->get();

    //     $netIncome = $revenues->sum('saldo') - $hpp->sum('saldo') - $operatingExpenses->sum('saldo');

    //     return view('dashboard.reports.income_statement', compact('revenues', 'hpp', 'operatingExpenses', 'netIncome', 'period', 'date'));
    // }

    public function incomeStatement(Request $request)
{
    $request->validate([
        'period' => 'sometimes|in:monthly,yearly',
        'date' => 'sometimes|date'
    ]);

    $period = $request->get('period', 'monthly');
    $dateInput = $request->get('date', Carbon::now()->format('Y-m-d')); // Ganti nama variabel agar tidak bentrok dengan $date object nanti
    $parsedDate = Carbon::parse($dateInput); // Parse sekali saja

    try {
        $baseQuery = JournalDetail::join('journal_entries', 'journal_details.journal_entry_id', '=', 'journal_entries.id')
            ->join('chart_of_accounts', 'journal_details.coa_id', '=', 'chart_of_accounts.id');

        if ($period == 'monthly') {
            $baseQuery->whereMonth('journal_entries.entry_date', $parsedDate->month)
                      ->whereYear('journal_entries.entry_date', $parsedDate->year);
        } elseif ($period == 'yearly') {
            $baseQuery->whereYear('journal_entries.entry_date', $parsedDate->year);
        }

        // Pendapatan Kotor (Gross Revenue)
        $grossRevenuesData = (clone $baseQuery)->where('chart_of_accounts.type', 'revenue')
            ->selectRaw('chart_of_accounts.name, chart_of_accounts.code, SUM(journal_details.credit) as amount') // Pastikan SUM dari tabel yang benar
            ->groupBy('chart_of_accounts.name', 'chart_of_accounts.code')
            ->orderBy('chart_of_accounts.code')
            ->get();
        $totalGrossRevenue = $grossRevenuesData->sum('amount');

        // Akun Kontra-Pendapatan (misalnya, Diskon Penjualan, Retur Penjualan)
        $contraRevenuesData = (clone $baseQuery)->where('chart_of_accounts.type', 'contra_revenue')
            ->selectRaw('chart_of_accounts.name, chart_of_accounts.code, SUM(journal_details.debit) as amount') // Kontra-akun biasanya di-sum dari sisi debit
            ->groupBy('chart_of_accounts.name', 'chart_of_accounts.code')
            ->orderBy('chart_of_accounts.code')
            ->get();
        $totalContraRevenue = $contraRevenuesData->sum('amount');

        // Pendapatan Bersih
        $totalNetRevenue = $totalGrossRevenue - $totalContraRevenue;

        // HPP (Gunakan kategori jika memungkinkan, bukan LIKE)
        // Misal, jika Anda sudah mengubah 'chart_of_accounts' untuk punya kolom 'category'
        // $hpp = (clone $baseQuery)->where('chart_of_accounts.category', 'COGS') 
        $hppData = (clone $baseQuery)->where('chart_of_accounts.name', 'LIKE', '%HPP%') // Sementara tetap pakai ini jika belum diubah
            ->selectRaw('chart_of_accounts.name, chart_of_accounts.code, SUM(journal_details.debit) as amount')
            ->groupBy('chart_of_accounts.name', 'chart_of_accounts.code')
            ->orderBy('chart_of_accounts.code')
            ->get();
        $totalHpp = $hppData->sum('amount');

        // Beban Operasional
        // Misal, jika HPP sudah pakai kategori COGS, maka 'expense' tidak perlu NOT LIKE HPP
        $operatingExpensesData = (clone $baseQuery)->where('chart_of_accounts.type', 'expense')
            ->where(function ($query) { // Tambahkan kondisi untuk mengecualikan akun HPP dari 'expense' jika masih pakai LIKE
                $query->where('chart_of_accounts.name', 'NOT LIKE', '%HPP%')
                      ->orWhereNull('chart_of_accounts.name'); // Atau jika nama bisa null dan tetap tipe expense
            })
            ->selectRaw('chart_of_accounts.name, chart_of_accounts.code, SUM(journal_details.debit) as amount')
            ->groupBy('chart_of_accounts.name', 'chart_of_accounts.code')
            ->orderBy('chart_of_accounts.code')
            ->get();
        $totalOperatingExpenses = $operatingExpensesData->sum('amount');

        // Laba Kotor
        $grossProfit = $totalNetRevenue - $totalHpp; // Gunakan Pendapatan Bersih

        // Laba Bersih
        $netIncome = $grossProfit - $totalOperatingExpenses;

        return view('dashboard.reports.income_statement', compact(
            'grossRevenuesData',        // Ganti nama variabel agar jelas
            'totalGrossRevenue',
            'contraRevenuesData',       // Kirim data kontra-pendapatan
            'totalContraRevenue',
            'totalNetRevenue',          // Kirim pendapatan bersih
            'hppData',                  // Ganti nama variabel
            'totalHpp',
            'grossProfit',
            'operatingExpensesData',    // Ganti nama variabel
            'totalOperatingExpenses',
            'netIncome',
            'period',
            'dateInput'                 // Kirim tanggal input awal
        ));
    } catch (\Exception $e) {
        // \Log::error('Error generating income statement: ' . $e->getMessage() . "\n" . $e->getTraceAsString()); // Log error lebih detail
        return redirect()->back()->with('error', 'Gagal memuat laporan: ' . $e->getMessage());
    }
}


    // public function cashFlow(Request $request)
    // {
    //     $period = $request->get('period', 'monthly');
    //     $date = $request->get('date', Carbon::now()->format('Y-m-d'));

    //     $query = JournalDetail::join('journal_entries', 'journal_details.journal_entry_id', '=', 'journal_entries.id')
    //         ->join('chart_of_accounts', 'journal_details.coa_id', '=', 'chart_of_accounts.id');

    //     if ($period == 'monthly') {
    //         $query->whereMonth('journal_entries.entry_date', Carbon::parse($date)->month)
    //             ->whereYear('journal_entries.entry_date', Carbon::parse($date)->year);
    //     } elseif ($period == 'yearly') {
    //         $query->whereYear('journal_entries.entry_date', Carbon::parse($date)->year);
    //     }

    //     // Arus Kas dari Aktivitas Operasional
    //     $operatingCashIn = (clone $query)->where('chart_of_accounts.type', 'revenue')->sum('credit');
    //     $operatingCashOut = (clone $query)->where('chart_of_accounts.type', 'expense')->sum('debit');

    //     // Arus Kas dari Aktivitas Investasi
    //     $investmentCashIn = (clone $query)->where('chart_of_accounts.type', 'asset')->where('chart_of_accounts.name', 'LIKE', '%Investasi%')->sum('credit');
    //     $investmentCashOut = (clone $query)->where('chart_of_accounts.type', 'asset')->where('chart_of_accounts.name', 'LIKE', '%Investasi%')->sum('debit');

    //     // Arus Kas dari Aktivitas Pendanaan
    //     $financingCashIn = (clone $query)->where('chart_of_accounts.type', 'equity')->sum('credit')
    //         + (clone $query)->where('chart_of_accounts.type', 'liability')->sum('credit');

    //     $financingCashOut = (clone $query)->where('chart_of_accounts.type', 'liability')->sum('debit');

    //     $netCashFlow = ($operatingCashIn - $operatingCashOut) + ($investmentCashIn - $investmentCashOut) + ($financingCashIn - $financingCashOut);

    //     return view('dashboard.reports.cash_flow', compact(
    //         'operatingCashIn',
    //         'operatingCashOut',
    //         'investmentCashIn',
    //         'investmentCashOut',
    //         'financingCashIn',
    //         'financingCashOut',
    //         'netCashFlow',
    //         'period',
    //         'date'
    //     ));
    // }

    public function cashFlow(Request $request)
    {
        $request->validate([
            'period' => 'sometimes|in:monthly,yearly',
            'date' => 'sometimes|date'
        ]);

        $period = $request->get('period', 'monthly');
        $date = $request->get('date', Carbon::now()->format('Y-m-d'));

        try {
            $query = JournalDetail::join('journal_entries', 'journal_details.journal_entry_id', '=', 'journal_entries.id')
                ->join('chart_of_accounts', 'journal_details.coa_id', '=', 'chart_of_accounts.id');

            if ($period == 'monthly') {
                $query->whereMonth('journal_entries.entry_date', Carbon::parse($date)->month)
                    ->whereYear('journal_entries.entry_date', Carbon::parse($date)->year);
            } elseif ($period == 'yearly') {
                $query->whereYear('journal_entries.entry_date', Carbon::parse($date)->year);
            }

            // Saldo awal kas
            $beginningCashQuery = JournalDetail::join('journal_entries', 'journal_details.journal_entry_id', '=', 'journal_entries.id')
                ->join('chart_of_accounts', 'journal_details.coa_id', '=', 'chart_of_accounts.id')
                ->where('chart_of_accounts.type', 'asset')
                ->where('chart_of_accounts.name', 'LIKE', '%Kas%');

            if ($period == 'monthly') {
                $beginningCashQuery->where('journal_entries.entry_date', '<', Carbon::parse($date)->startOfMonth());
            } else {
                $beginningCashQuery->where('journal_entries.entry_date', '<', Carbon::parse($date)->startOfYear());
            }

            $beginningCashBalance = $beginningCashQuery->sum('debit') - $beginningCashQuery->sum('credit');

            // Arus Kas dari Aktivitas Operasional
            $operatingActivities = (clone $query)
                ->where(function ($q) {
                    $q->where('chart_of_accounts.type', 'revenue')
                        ->orWhere('chart_of_accounts.type', 'expense')
                        ->orWhere('chart_of_accounts.name', 'LIKE', '%Piutang%')
                        ->orWhere('chart_of_accounts.name', 'LIKE', '%Utang%');
                })
                ->selectRaw('chart_of_accounts.name, chart_of_accounts.code, 
                SUM(credit) as cash_in, 
                SUM(debit) as cash_out')
                ->groupBy('chart_of_accounts.name', 'chart_of_accounts.code')
                ->orderBy('chart_of_accounts.code')
                ->get();

            $operatingCashIn = $operatingActivities->sum('cash_in');
            $operatingCashOut = $operatingActivities->sum('cash_out');

            // Arus Kas dari Aktivitas Investasi
            $investmentActivities = (clone $query)
                ->where(function ($q) {
                    $q->where('chart_of_accounts.name', 'LIKE', '%Aktiva Tetap%')
                        ->orWhere('chart_of_accounts.name', 'LIKE', '%Investasi%')
                        ->orWhere('chart_of_accounts.name', 'LIKE', '%Peralatan%');
                })
                ->selectRaw('chart_of_accounts.name, chart_of_accounts.code, 
                SUM(credit) as cash_in, 
                SUM(debit) as cash_out')
                ->groupBy('chart_of_accounts.name', 'chart_of_accounts.code')
                ->orderBy('chart_of_accounts.code')
                ->get();

            $investmentCashIn = $investmentActivities->sum('cash_in');
            $investmentCashOut = $investmentActivities->sum('cash_out');

            // Arus Kas dari Aktivitas Pendanaan
            $financingActivities = (clone $query)
                ->where(function ($q) {
                    $q->where('chart_of_accounts.type', 'equity')
                        ->orWhere('chart_of_accounts.type', 'liability')
                        ->where('chart_of_accounts.name', 'NOT LIKE', '%Utang Usaha%');
                })
                ->selectRaw('chart_of_accounts.name, chart_of_accounts.code, 
                SUM(credit) as cash_in, 
                SUM(debit) as cash_out')
                ->groupBy('chart_of_accounts.name', 'chart_of_accounts.code')
                ->orderBy('chart_of_accounts.code')
                ->get();

            $financingCashIn = $financingActivities->sum('cash_in');
            $financingCashOut = $financingActivities->sum('cash_out');

            // Perhitungan total
            $netOperating = $operatingCashIn - $operatingCashOut;
            $netInvesting = $investmentCashIn - $investmentCashOut;
            $netFinancing = $financingCashIn - $financingCashOut;
            $netCashFlow = $netOperating + $netInvesting + $netFinancing;
            $endingCashBalance = $beginningCashBalance + $netCashFlow;

            return view('dashboard.reports.cash_flow', compact(
                'beginningCashBalance',
                'operatingActivities',
                'netOperating',
                'investmentActivities',
                'netInvesting',
                'financingActivities',
                'netFinancing',
                'netCashFlow',
                'endingCashBalance',
                'period',
                'date'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat laporan: ' . $e->getMessage());
        }
    }
}
