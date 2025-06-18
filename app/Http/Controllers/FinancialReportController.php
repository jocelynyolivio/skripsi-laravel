<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\JournalDetail;
use App\Models\JournalEntry;

class FinancialReportController extends Controller
{

    public function balanceSheet(Request $request)
    {
        $periodForDisplay = $request->get('period', 'monthly');
        $dateInput = $request->get('date', Carbon::now()->format('Y-m-d'));
        $parsedDate = Carbon::parse($dateInput);

        try {
            $coaBalancesQuery = JournalDetail::selectRaw(
                'chart_of_accounts.id as coa_id,
                 chart_of_accounts.name as coa_name,
                 chart_of_accounts.code as coa_code,
                 chart_of_accounts.type,
                 SUM(journal_details.debit) as total_debit,
                 SUM(journal_details.credit) as total_credit'
            )
                ->join('journal_entries', 'journal_details.journal_entry_id', '=', 'journal_entries.id')
                ->join('chart_of_accounts', 'journal_details.coa_id', '=', 'chart_of_accounts.id')
                ->whereIn('chart_of_accounts.type', ['asset', 'contra_asset', 'liability', 'equity'])
                ->whereDate('journal_entries.entry_date', '<=', $parsedDate->toDateString());

            $coaSummary = $coaBalancesQuery->groupBy('chart_of_accounts.id', 'chart_of_accounts.name', 'chart_of_accounts.code', 'chart_of_accounts.type')
                ->orderBy('chart_of_accounts.code')
                ->get();

            // Laba/rugi ini akan mempengaruhi bagian Ekuitas jadi Laba Ditahan
            $cumulativeProfitOrLossQuery = JournalDetail::selectRaw(
                // Pendapatan Bersih = (Pendapatan Kotor - Nilai Kontra Pendapatan)
                // Pendapatan Kotor (saldo normal Kredit): (SUM Kredit tipe 'revenue' - SUM Debit tipe 'revenue')
                // Nilai Kontra Pendapatan (saldo normal Debit): (SUM Debit tipe 'contra_revenue' - SUM Kredit tipe 'contra_revenue')
                '((SUM(CASE WHEN chart_of_accounts.type = \'revenue\' THEN journal_details.credit ELSE 0 END) -
                   SUM(CASE WHEN chart_of_accounts.type = \'revenue\' THEN journal_details.debit ELSE 0 END)) -
                  (SUM(CASE WHEN chart_of_accounts.type = \'contra_revenue\' THEN journal_details.debit ELSE 0 END) -
                   SUM(CASE WHEN chart_of_accounts.type = \'contra_revenue\' THEN journal_details.credit ELSE 0 END))) - ' .
                    // Beban Bersih = (Beban Kotor - Nilai Kontra Beban)
                    // Beban Kotor (saldo normal Debit): (SUM Debit tipe 'expense'/'cogs' - SUM Kredit tipe 'expense'/'cogs')
                    // Nilai Kontra Beban (saldo normal Kredit): (SUM Kredit tipe 'contra_expense' - SUM Debit tipe 'contra_expense')
                    '((SUM(CASE WHEN chart_of_accounts.type IN (\'expense\', \'cogs\') THEN journal_details.debit ELSE 0 END) -
                   SUM(CASE WHEN chart_of_accounts.type IN (\'expense\', \'cogs\') THEN journal_details.credit ELSE 0 END)) -
                  (SUM(CASE WHEN chart_of_accounts.type = \'contra_expense\' THEN journal_details.credit ELSE 0 END) -
                   SUM(CASE WHEN chart_of_accounts.type = \'contra_expense\' THEN journal_details.debit ELSE 0 END)))
                as net_income'
            )
                ->join('journal_entries', 'journal_details.journal_entry_id', '=', 'journal_entries.id')
                ->join('chart_of_accounts', 'journal_details.coa_id', '=', 'chart_of_accounts.id')
                ->whereIn('chart_of_accounts.type', ['revenue', 'contra_revenue', 'expense', 'cogs', 'contra_expense'])
                ->whereDate('journal_entries.entry_date', '<=', $parsedDate->toDateString());

            $profitOrLossResult = $cumulativeProfitOrLossQuery->first();
            $netIncomeForEquity = $profitOrLossResult ? $profitOrLossResult->net_income : 0;

            // --- Menggabungkan Laba/Rugi Kumulatif ke Akun Laba Ditahan di Ekuitas ---
            $retainedEarningsCoaCode = '3-10002'; // Kode akun untuk 'Laba Ditahan' dari COA Anda
            $existingRetainedEarnings = $coaSummary->firstWhere('coa_code', $retainedEarningsCoaCode);

            if ($existingRetainedEarnings) {
                // Jika akun Laba Ditahan sudah ada (misalnya dari saldo awal atau tutup buku sebelumnya),
                // tambahkan laba/rugi periode berjalan kumulatif ke saldo akun tersebut.
                // Laba akan menambah sisi kredit, Rugi akan menambah sisi debit (atau mengurangi kredit).
                if ($netIncomeForEquity >= 0) { // Jika Laba
                    $existingRetainedEarnings->total_credit += $netIncomeForEquity;
                } else { // Jika Rugi
                    $existingRetainedEarnings->total_debit += abs($netIncomeForEquity);
                }
            } else {
                // Jika akun Laba Ditahan belum ada di $coaSummary (misalnya, ini laporan pertama kali),
                // buat entri baru untuk "Akumulasi Laba/Rugi" atau "Laba Ditahan".
                if ($netIncomeForEquity != 0) { // Hanya tambahkan jika ada laba/rugi
                    $coaSummary->push((object)[
                        'coa_id' => 'RE_AUTO', // ID sementara
                        'coa_code' => $retainedEarningsCoaCode, // Gunakan kode CoA Laba Ditahan
                        'coa_name' => 'Laba Ditahan (Akumulasi Periode Berjalan)',
                        'type' => 'equity',
                        'total_debit' => ($netIncomeForEquity < 0) ? abs($netIncomeForEquity) : 0,
                        'total_credit' => ($netIncomeForEquity > 0) ? $netIncomeForEquity : 0,
                    ]);
                }
            }

            // --- Memisahkan Akun berdasarkan Tipe untuk Ditampilkan di View ---
            // Akun 'contra_asset' dikelompokkan bersama 'asset'
            $assets = $coaSummary->whereIn('type', ['asset', 'contra_asset'])->values(); // values() untuk reindex array
            $liabilities = $coaSummary->where('type', 'liability')->values();
            $equities = $coaSummary->where('type', 'equity')->values();

            // --- Menghitung Saldo Akhir per Akun dan Total per Kategori ---
            $totalAssets = 0;
            foreach ($assets as $item) {
                // Saldo normal Aset adalah Debit. Kontra Aset saldo normalnya Kredit.
                // Jadi, (Total Debit - Total Kredit) akan memberikan saldo bersih.
                // Untuk Kontra Aset, ini akan menghasilkan nilai negatif yang akan mengurangi Total Aset.
                $item->balance = $item->total_debit - $item->total_credit;
                $totalAssets += $item->balance;
            }

            $totalLiabilities = 0;
            foreach ($liabilities as $item) {
                // Saldo normal Liabilitas adalah Kredit.
                $item->balance = $item->total_credit - $item->total_debit;
                $totalLiabilities += $item->balance;
            }

            $totalEquities = 0;
            foreach ($equities as $item) {
                // Saldo normal Ekuitas adalah Kredit.
                $item->balance = $item->total_credit - $item->total_debit;
                $totalEquities += $item->balance;
            }

            // Mengirim data ke view
            return view('dashboard.reports.balance_sheet', compact(
                'periodForDisplay',
                'dateInput',
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

    public function incomeStatement(Request $request)
    {
        $request->validate([
            'period' => 'sometimes|in:monthly,yearly',
            'date' => 'sometimes|date'
        ]);

        $period = $request->get('period', 'monthly');
        $dateInput = $request->get('date', Carbon::now()->format('Y-m-d'));
        $parsedDate = Carbon::parse($dateInput);

        try {
            // Query dasar untuk mengambil detail jurnal berdasarkan periode
            $baseQuery = JournalDetail::join('journal_entries', 'journal_details.journal_entry_id', '=', 'journal_entries.id')
                ->join('chart_of_accounts', 'journal_details.coa_id', '=', 'chart_of_accounts.id');

            // Filter berdasarkan periode yang dipilih (bulanan atau tahunan)
            if ($period == 'monthly') {
                $baseQuery->whereMonth('journal_entries.entry_date', $parsedDate->month)
                    ->whereYear('journal_entries.entry_date', $parsedDate->year);
            } elseif ($period == 'yearly') {
                $baseQuery->whereYear('journal_entries.entry_date', $parsedDate->year);
            }

            // --- PENDAPATAN ---
            // 1. Pendapatan Kotor (Gross Revenue)
            $grossRevenuesData = (clone $baseQuery) // Clone baseQuery agar filter periode tidak hilang
                ->where('chart_of_accounts.type', 'revenue')
                ->selectRaw('chart_of_accounts.name, chart_of_accounts.code, SUM(journal_details.credit - journal_details.debit) as amount') // Saldo bersih kredit
                ->groupBy('chart_of_accounts.name', 'chart_of_accounts.code')
                ->orderBy('chart_of_accounts.code')
                ->get();
            $totalGrossRevenue = $grossRevenuesData->sum('amount');

            // 2. Akun Kontra-Pendapatan (misalnya, Diskon Penjualan)
            $contraRevenuesData = (clone $baseQuery)
                ->where('chart_of_accounts.type', 'contra_revenue')
                ->selectRaw('chart_of_accounts.name, chart_of_accounts.code, SUM(journal_details.debit - journal_details.credit) as amount') // Saldo bersih debit (nilai pengurang)
                ->groupBy('chart_of_accounts.name', 'chart_of_accounts.code')
                ->orderBy('chart_of_accounts.code')
                ->get();
            $totalContraRevenue = $contraRevenuesData->sum('amount');

            // 3. Pendapatan Bersih (Net Revenue)
            $totalNetRevenue = $totalGrossRevenue - $totalContraRevenue;

            // --- HARGA POKOK PENJUALAN (HPP / COGS) ---
            // Anda memiliki 'HPP Bahan Dental' dengan tipe 'expense'.
            // Jika ada akun HPP lain atau ingin memisahkannya, bisa gunakan filter spesifik atau kategori COA.
            $hppData = (clone $baseQuery)
                ->where('chart_of_accounts.code', '5-10002') // Menggunakan kode akun spesifik untuk HPP Bahan Dental
                // atau ->where('chart_of_accounts.name', 'HPP Bahan Dental')
                // atau jika Anda punya kategori khusus untuk HPP/COGS di tabel CoA
                ->selectRaw('chart_of_accounts.name, chart_of_accounts.code, SUM(journal_details.debit - journal_details.credit) as amount') // Saldo bersih debit
                ->groupBy('chart_of_accounts.name', 'chart_of_accounts.code')
                ->orderBy('chart_of_accounts.code')
                ->get();
            $totalHpp = $hppData->sum('amount');

            // Laba Kotor (Gross Profit)
            $grossProfit = $totalNetRevenue - $totalHpp;

            // --- BEBAN OPERASIONAL ---
            // 1. Beban Operasional Kotor (Gross Operating Expenses)
            $operatingExpensesData = (clone $baseQuery)
                ->where('chart_of_accounts.type', 'expense')
                // Kecualikan akun HPP jika sudah dihitung terpisah
                ->where('chart_of_accounts.code', '!=', '5-10002') // Hindari penghitungan ganda HPP
                ->selectRaw('chart_of_accounts.name, chart_of_accounts.code, SUM(journal_details.debit - journal_details.credit) as amount') // Saldo bersih debit
                ->groupBy('chart_of_accounts.name', 'chart_of_accounts.code')
                ->orderBy('chart_of_accounts.code')
                ->get();
            $totalGrossOperatingExpenses = $operatingExpensesData->sum('amount');

            // 2. Akun Kontra-Beban (misalnya, Diskon Pembelian)
            $contraExpensesData = (clone $baseQuery)
                ->where('chart_of_accounts.type', 'contra_expense')
                ->selectRaw('chart_of_accounts.name, chart_of_accounts.code, SUM(journal_details.credit - journal_details.debit) as amount') // Saldo bersih kredit (nilai pengurang beban)
                ->groupBy('chart_of_accounts.name', 'chart_of_accounts.code')
                ->orderBy('chart_of_accounts.code')
                ->get();
            $totalContraExpense = $contraExpensesData->sum('amount');

            // 3. Beban Operasional Bersih (Net Operating Expenses)
            $totalNetOperatingExpenses = $totalGrossOperatingExpenses - $totalContraExpense;

            // Laba Bersih (Net Income)
            $netIncome = $grossProfit - $totalNetOperatingExpenses;

            return view('dashboard.reports.income_statement', compact(
                'grossRevenuesData',
                'totalGrossRevenue',
                'contraRevenuesData',
                'totalContraRevenue',
                'totalNetRevenue',
                'hppData',
                'totalHpp',
                'grossProfit',
                'operatingExpensesData',        // Ini adalah detail Beban Operasional Kotor
                'totalGrossOperatingExpenses',  // Ini adalah total Beban Operasional Kotor
                'contraExpensesData',           // Detail Kontra Beban
                'totalContraExpense',           // Total Kontra Beban
                // 'totalNetOperatingExpenses', // Opsional, bisa dihitung di view atau kirim
                'netIncome',
                'period',
                'dateInput'
            ));
        } catch (\Exception $e) {
            // \Log::error('Error generating income statement: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Gagal memuat laporan laba rugi: ' . $e->getMessage());
        }
    }
    public function cashFlow(Request $request)
    {
        $request->validate([
            'period' => 'sometimes|string|in:monthly,yearly',
            'date' => 'sometimes|string|date'
        ]);

        $period = $request->get('period', 'monthly');
        $dateInput = $request->get('date', Carbon::now()->format('Y-m-d'));
        $parsedDate = Carbon::parse($dateInput);

        try {
            // --- Tentukan Rentang Tanggal Laporan ---
            $startDateOfPeriod = null;
            $endDateOfPeriod = null;

            if ($period == 'monthly') {
                $startDateOfPeriod = $parsedDate->copy()->startOfMonth();
                $endDateOfPeriod = $parsedDate->copy()->endOfMonth();
            } else { // yearly
                $startDateOfPeriod = $parsedDate->copy()->startOfYear();
                $endDateOfPeriod = $parsedDate->copy()->endOfYear();
            }

            // --- Saldo Awal Kas dan Setara Kas ---
            // PERUBAHAN: Menggunakan relasi 'account' untuk mengakses ChartOfAccount dari JournalDetail
            $beginningCashResult = JournalDetail::join('journal_entries', 'journal_details.journal_entry_id', '=', 'journal_entries.id')
                ->join('chart_of_accounts', 'journal_details.coa_id', '=', 'chart_of_accounts.id') // Join tetap berdasarkan coa_id
                ->where('chart_of_accounts.is_cash_equivalent', true)
                ->where('journal_entries.entry_date', '<', $startDateOfPeriod->toDateString())
                ->selectRaw('SUM(journal_details.debit) as total_debit, SUM(journal_details.credit) as total_credit')
                ->first();

            $beginningCashBalance = ($beginningCashResult->total_debit ?? 0) - ($beginningCashResult->total_credit ?? 0);

            // --- Dapatkan Semua JURNAL ENTRI yang Memiliki Detail Kas/Bank dalam Periode ---
            // PERUBAHAN: Menggunakan relasi 'details.account'
            $journalEntriesInPeriod = JournalEntry::whereBetween('entry_date', [$startDateOfPeriod->toDateString(), $endDateOfPeriod->toDateString()])
                ->whereHas('details.account', function ($query) { // <--- DIUBAH dari 'details.chartOfAccount'
                    $query->where('is_cash_equivalent', true);
                })
                ->with(['details.account']) // <--- DIUBAH dari 'details.chartOfAccount'
                ->orderBy('journal_entries.entry_date', 'asc')
                ->orderBy('journal_entries.id', 'asc')
                ->get();

            $groupedFlows = [
                'operating' => [],
                'investing' => [],
                'financing' => [],
            ];

            foreach ($journalEntriesInPeriod as $entry) {
                // PERUBAHAN: Menggunakan relasi 'account' untuk mengakses ChartOfAccount dari JournalDetail
                $cashDetails = $entry->details->filter(function ($detail) {
                    return $detail->account && $detail->account->is_cash_equivalent; // <--- DIUBAH
                });

                $nonCashDetails = $entry->details->filter(function ($detail) {
                    return $detail->account && !$detail->account->is_cash_equivalent; // <--- DIUBAH
                });

                if ($cashDetails->isEmpty() || $nonCashDetails->isEmpty()) {
                    continue;
                }

                $netCashMovementInEntry = $cashDetails->sum(function ($detail) {
                    return $detail->debit - $detail->credit;
                });

                if (abs($netCashMovementInEntry) < 0.01) {
                    continue;
                }

                $isCashInflow = $netCashMovementInEntry > 0;

                $primaryNonCashDetail = $nonCashDetails->first();
                // PERUBAHAN: Menggunakan relasi 'account'
                if (!$primaryNonCashDetail || !$primaryNonCashDetail->account) { // <--- DIUBAH
                    Log::warning("Cash flow: Entry ID {$entry->id} has cash movement but no clear primary non-cash CoA object.");
                    continue;
                }
                $nonCashCoa = $primaryNonCashDetail->account; // <--- DIUBAH

                $flowClassification = $this->classifyCashFlow($nonCashCoa, $isCashInflow, $entry);

                if ($flowClassification['activity'] === 'none' || empty($flowClassification['description'])) {
                    Log::info("Cash flow: Entry ID {$entry->id} - NonCashCoa '{$nonCashCoa->name}' (code: {$nonCashCoa->code}) resulted in 'none' activity or empty description. NonCashDetail ID: {$primaryNonCashDetail->id}");
                    continue;
                }

                $activityKey = $flowClassification['activity'];
                $descriptionKey = $flowClassification['description'];
                $coaCodeRef = $flowClassification['coa_code_reference'] ?? $nonCashCoa->code;

                if (!isset($groupedFlows[$activityKey][$descriptionKey])) {
                    $groupedFlows[$activityKey][$descriptionKey] = [
                        'coa_code' => $coaCodeRef,
                        'coa_name_display' => $descriptionKey,
                        'amount' => 0,
                    ];
                }
                $groupedFlows[$activityKey][$descriptionKey]['amount'] += $netCashMovementInEntry;
            }

            $operatingActivitiesData = $this->formatFlowsForView($groupedFlows['operating'] ?? []);
            $investmentActivitiesData = $this->formatFlowsForView($groupedFlows['investing'] ?? []);
            $financingActivitiesData = $this->formatFlowsForView($groupedFlows['financing'] ?? []);

            $netOperatingCashFlow = $operatingActivitiesData->sum(fn($item) => ($item->inferred_cash_in ?? 0) - ($item->inferred_cash_out ?? 0));
            $netInvestmentCashFlow = $investmentActivitiesData->sum(fn($item) => ($item->inferred_cash_in ?? 0) - ($item->inferred_cash_out ?? 0));
            $netFinancingCashFlow = $financingActivitiesData->sum(fn($item) => ($item->inferred_cash_in ?? 0) - ($item->inferred_cash_out ?? 0));

            $netCashFlowChange = $netOperatingCashFlow + $netInvestmentCashFlow + $netFinancingCashFlow;
            $endingCashBalance = $beginningCashBalance + $netCashFlowChange;

            return view('dashboard.reports.cash_flow', compact(
                'beginningCashBalance',
                'operatingActivitiesData',
                'netOperatingCashFlow',
                'investmentActivitiesData',
                'netInvestmentCashFlow',
                'financingActivitiesData',
                'netFinancingCashFlow',
                'netCashFlowChange',
                'endingCashBalance',
                'period',
                'dateInput',
                'parsedDate'
            ));
        } catch (\Exception $e) {
            Log::error('Cash Flow Controller Error: ' . $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine() . "\n" . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Gagal memuat laporan arus kas: Terjadi kesalahan sistem.');
        }
    }

    /**
     * Mengklasifikasikan arus kas berdasarkan akun non-kas dan arahnya.
     * Ini adalah fungsi PENTING yang perlu Anda KUSTOMISASI secara mendalam!
     * @return array ['activity' => string, 'description' => string, 'coa_code_reference' => string]
     */
    protected function classifyCashFlow(ChartOfAccount $nonCashCoa, bool $isCashInflow, JournalEntry $entry): array
    {
        // Pastikan $nonCashCoa tidak null, meskipun sudah dicek sebelumnya
        if (!$nonCashCoa) {
            Log::warning("classifyCashFlow called with null nonCashCoa for entry ID: {$entry->id}");
            return ['activity' => 'none', 'description' => 'Error: Akun non-kas tidak terdefinisi', 'coa_code_reference' => 'ERR'];
        }

        $activity = $nonCashCoa->cash_flow_activity ?: 'none';
        $description = $nonCashCoa->name;
        $coaCodeReference = $nonCashCoa->code;

        // Logika Klasifikasi Detail (CONTOH DASAR - PERLU DIKEMBANGKAN)
        if ($activity === 'operating') {
            if ($isCashInflow) {
                if (str_contains(strtolower($nonCashCoa->name), 'piutang usaha') || $nonCashCoa->type === 'revenue') {
                    $description = 'Penerimaan dari Pelanggan';
                } elseif (str_contains(strtolower($nonCashCoa->name), 'utang usaha')) {
                    $description = 'Penerimaan Terkait Utang Usaha (misal: Refund)';
                } else {
                    $description = 'Penerimaan Operasional Lainnya (' . $nonCashCoa->name . ')';
                }
            } else { // Cash Outflow
                if (str_contains(strtolower($nonCashCoa->name), 'utang usaha')) {
                    $description = 'Pembayaran kepada Pemasok';
                } elseif ($nonCashCoa->type === 'expense' || str_contains(strtolower($nonCashCoa->name), 'persediaan') || $nonCashCoa->type === 'cogs') {
                    $description = 'Pembayaran Beban Operasional/Pemasok (' . $nonCashCoa->name . ')';
                } elseif (str_contains(strtolower($nonCashCoa->name), 'utang pajak')) {
                    $description = 'Pembayaran Pajak';
                } else {
                    $description = 'Pengeluaran Operasional Lainnya (' . $nonCashCoa->name . ')';
                }
            }
        } elseif ($activity === 'investing') {
            if ($isCashInflow) {
                $description = 'Penjualan Aset (' . $nonCashCoa->name . ')';
            } else {
                $description = 'Pembelian Aset (' . $nonCashCoa->name . ')';
            }
        } elseif ($activity === 'financing') {
            if ($isCashInflow) {
                if (in_array(strtolower($nonCashCoa->type), ['equity']) || (str_contains(strtolower($nonCashCoa->name), 'modal') || str_contains(strtolower($nonCashCoa->name), 'pinjaman diterima'))) {
                    $description = 'Penerimaan dari Pendanaan (' . $nonCashCoa->name . ')';
                } else {
                    $description = 'Penerimaan Pendanaan Lainnya (' . $nonCashCoa->name . ')';
                }
            } else {
                if (in_array(strtolower($nonCashCoa->type), ['equity']) || (str_contains(strtolower($nonCashCoa->name), 'prive') || str_contains(strtolower($nonCashCoa->name), 'dividen') || str_contains(strtolower($nonCashCoa->name), 'pembayaran pinjaman'))) {
                    $description = 'Pembayaran untuk Pendanaan (' . $nonCashCoa->name . ')';
                } else {
                    $description = 'Pengeluaran Pendanaan Lainnya (' . $nonCashCoa->name . ')';
                }
            }
        } else {
            $description = 'Lain-lain (' . $nonCashCoa->name . ')';
            // Jika activity 'none' dari CoA, kita mungkin ingin default ke 'operating' atau log sebagai unclassified.
            // Atau bisa juga dikembalikan sebagai 'none' dan di-filter di loop utama.
            // Untuk saat ini, jika $activity adalah 'none', biarkan, nanti akan di-skip oleh logika di loop utama.
        }
        return ['activity' => $activity, 'description' => $description, 'coa_code_reference' => $coaCodeReference];
    }

    protected function formatFlowsForView(array $groupedFlows): Collection
    {
        $formattedData = new Collection();
        foreach ($groupedFlows as $data) { // $description (sekarang key) tidak dipakai langsung, $data adalah value-nya
            $amount = $data['amount'] ?? 0;
            if (abs($amount) < 0.01) continue;

            $formattedData->push((object)[
                'coa_code' => $data['coa_code'] ?? '',
                'coa_name' => $data['coa_name_display'] ?? 'Unknown Description',
                'inferred_cash_in' => $amount > 0 ? $amount : 0,
                'inferred_cash_out' => $amount < 0 ? abs($amount) : 0,
            ]);
        }
        return $formattedData->sortBy(function ($item) {
            // Prioritaskan inflow, lalu urutkan berdasarkan nama/deskripsi
            $sortPrefix = $item->inferred_cash_in > 0 ? 'A' : 'B';
            return $sortPrefix . '_' . $item->coa_name;
        })->values();
    }


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
    // public function balanceSheet(Request $request)
    // {
    //     $periodForDisplay = $request->get('period', 'monthly'); // Digunakan untuk display atau jika ada logika P/L spesifik periode
    //     $dateInput = $request->get('date', Carbon::now()->format('Y-m-d'));
    //     $parsedDate = Carbon::parse($dateInput);

    //     try {
    //         // Query untuk saldo akun Neraca (Aset, Liabilitas, Ekuitas)
    //         // Harus mengambil semua transaksi s/d tanggal laporan ($parsedDate)
    //         $coaBalancesQuery = JournalDetail::selectRaw(
    //             'chart_of_accounts.id as coa_id,
    //          chart_of_accounts.name as coa_name,
    //          chart_of_accounts.code as coa_code,  // Tambahkan coa_code jika diperlukan di view
    //          chart_of_accounts.type,
    //          SUM(journal_details.debit) as total_debit,
    //          SUM(journal_details.credit) as total_credit'
    //         )
    //             ->join('journal_entries', 'journal_details.journal_entry_id', '=', 'journal_entries.id')
    //             ->join('chart_of_accounts', 'journal_details.coa_id', '=', 'chart_of_accounts.id')
    //             ->whereIn('chart_of_accounts.type', ['asset', 'liability', 'equity']) // Hanya akun Neraca
    //             ->whereDate('journal_entries.entry_date', '<=', $parsedDate->toDateString()); // KRITIKAL: Hingga tanggal laporan

    //         // Jika Anda memiliki saldo awal akun (opening balances) yang disimpan terpisah dan tidak melalui jurnal,
    //         // Anda perlu menggabungkannya di sini. Untuk saat ini, kita asumsikan semua dari jurnal.

    //         $coaSummary = $coaBalancesQuery->groupBy('chart_of_accounts.id', 'chart_of_accounts.name', 'chart_of_accounts.code', 'chart_of_accounts.type')
    //             ->orderBy('chart_of_accounts.code') // Urutkan berdasarkan kode akun
    //             ->get();

    //         // Penanganan Akun Kontra (Contoh: Akumulasi Penyusutan)
    //         // Akun kontra-aset seperti Akumulasi Penyusutan biasanya memiliki saldo normal kredit.
    //         // Perhitungan saldo aset (debit - kredit) akan secara otomatis menguranginya.
    //         // Jadi, logika swap manual seperti untuk "Diskon Pembelian" sebaiknya dihindari.
    //         // Jika "Diskon Pembelian" adalah kontra-aset (yang jarang), pastikan tipe CoA-nya benar
    //         // dan saldo normalnya (kredit) akan mengurangi total aset saat `debit - kredit` dihitung.
    //         // Saya akan menghapus logika swap untuk 'Diskon Pembelian' karena berpotensi salah
    //         // dan menyarankan untuk memperbaiki klasifikasi CoA jika akun tsb tidak sesuai.
    //         // Jika 'Diskon Pembelian' seharusnya mempengaruhi HPP, itu akan masuk ke perhitungan Laba/Rugi.

    //         // Perhitungan Laba/Rugi Kumulatif s/d Tanggal Laporan
    //         // Ini adalah laba/rugi yang akan menjadi bagian dari ekuitas (Laba Ditahan)
    //         $cumulativeProfitOrLossQuery = JournalDetail::selectRaw(
    //             '(SUM(CASE WHEN chart_of_accounts.type = \'revenue\' THEN journal_details.credit ELSE 0 END) -
    //           SUM(CASE WHEN chart_of_accounts.type = \'revenue\' THEN journal_details.debit ELSE 0 END)) -
    //          (SUM(CASE WHEN chart_of_accounts.type IN (\'expense\', \'cogs\') THEN journal_details.debit ELSE 0 END) -
    //           SUM(CASE WHEN chart_of_accounts.type IN (\'expense\', \'cogs\') THEN journal_details.credit ELSE 0 END))
    //          as net_income'
    //         )
    //             ->join('journal_entries', 'journal_details.journal_entry_id', '=', 'journal_entries.id')
    //             ->join('chart_of_accounts', 'journal_details.coa_id', '=', 'chart_of_accounts.id')
    //             // Asumsi 'contra_revenue' juga sudah diperhitungkan menjadi pengurang revenue di atas
    //             // Jika 'contra_revenue' punya type sendiri, perlu penyesuaian di query net_income
    //             ->whereIn('chart_of_accounts.type', ['revenue', 'contra_revenue', 'expense', 'cogs']) // Sesuaikan dengan tipe akun L/R Anda
    //             ->whereDate('journal_entries.entry_date', '<=', $parsedDate->toDateString()); // Kumulatif s/d tanggal laporan

    //         // Jika Anda ingin Laba/Rugi untuk PERIODE tertentu (misalnya hanya bulan/tahun ini) untuk ditampilkan
    //         // terpisah dari Laba Ditahan Awal, Anda bisa buat query tambahan dengan filter periode ($periodForDisplay)
    //         // Namun untuk total ekuitas di Neraca, yang dibutuhkan adalah laba/rugi kumulatif.

    //         $profitOrLossResult = $cumulativeProfitOrLossQuery->first();
    //         $netIncomeForEquity = $profitOrLossResult ? $profitOrLossResult->net_income : 0;

    //         // Cek apakah sudah ada akun Laba Ditahan (misalnya dari proses tutup buku sebelumnya)
    //         $existingRetainedEarnings = $coaSummary->firstWhere('coa_name', 'Laba Ditahan'); // Atau berdasarkan kode akun Laba Ditahan

    //         if ($existingRetainedEarnings) {
    //             // Jika ada, tambahkan net_income ke saldo Laba Ditahan yang ada
    //             // Ini asumsi net_income dihitung dari awal periode fiskal atau sejak tutup buku terakhir
    //             // Untuk kesederhanaan, kita update saldonya langsung.
    //             // Dalam sistem nyata, ini bisa lebih kompleks (saldo awal + P/L periode berjalan)
    //             if ($netIncomeForEquity > 0) {
    //                 $existingRetainedEarnings->total_credit += $netIncomeForEquity;
    //             } else {
    //                 $existingRetainedEarnings->total_debit += abs($netIncomeForEquity);
    //             }
    //         } else {
    //             // Jika tidak ada akun Laba Ditahan, tambahkan sebagai item baru
    //             // Ini menyiratkan $netIncomeForEquity adalah akumulasi laba/rugi sejak awal
    //             if ($netIncomeForEquity != 0) { // Hanya tambahkan jika ada laba/rugi
    //                 $coaSummary->push((object)[
    //                     'coa_id' => 'RE', // Beri ID unik sementara atau null
    //                     'coa_code' => '3-XXXX', // Beri kode akun sementara
    //                     'coa_name' => 'Akumulasi Laba/Rugi (Laba Ditahan)',
    //                     'type' => 'equity',
    //                     'total_debit' => ($netIncomeForEquity < 0) ? abs($netIncomeForEquity) : 0,
    //                     'total_credit' => ($netIncomeForEquity > 0) ? $netIncomeForEquity : 0,
    //                 ]);
    //             }
    //         }

    //         $assets = $coaSummary->where('type', 'asset')->values(); // Gunakan values() untuk reindex array
    //         $liabilities = $coaSummary->where('type', 'liability')->values();
    //         $equities = $coaSummary->where('type', 'equity')->values();

    //         // Hitung Saldo Akhir untuk setiap Akun dan Total
    //         $totalAssets = 0;
    //         foreach ($assets as $item) {
    //             // Akun kontra-aset (mis. Akumulasi Penyusutan) normalnya kredit
    //             $item->balance = $item->total_debit - $item->total_credit;
    //             $totalAssets += $item->balance;
    //         }

    //         $totalLiabilities = 0;
    //         foreach ($liabilities as $item) {
    //             $item->balance = $item->total_credit - $item->total_debit;
    //             $totalLiabilities += $item->balance;
    //         }

    //         $totalEquities = 0;
    //         foreach ($equities as $item) {
    //             $item->balance = $item->total_credit - $item->total_debit;
    //             $totalEquities += $item->balance;
    //         }

    //         return view('dashboard.reports.balance_sheet', compact(
    //             'periodForDisplay', // Untuk judul atau filter di view jika masih dipakai
    //             'dateInput',        // Untuk menampilkan tanggal laporan di view
    //             'assets',
    //             'liabilities',
    //             'equities',
    //             'totalAssets',
    //             'totalLiabilities',
    //             'totalEquities'
    //         ));
    //     } catch (\Exception $e) {
    //         // \Log::error('Error generating balance sheet: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
    //         return redirect()->back()->with('error', 'Gagal memuat laporan neraca: ' . $e->getMessage());
    //     }
    // }
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

    // public function incomeStatement(Request $request)
    // {
    //     $request->validate([
    //         'period' => 'sometimes|in:monthly,yearly',
    //         'date' => 'sometimes|date'
    //     ]);

    //     $period = $request->get('period', 'monthly');
    //     $dateInput = $request->get('date', Carbon::now()->format('Y-m-d')); // Ganti nama variabel agar tidak bentrok dengan $date object nanti
    //     $parsedDate = Carbon::parse($dateInput); // Parse sekali saja

    //     try {
    //         $baseQuery = JournalDetail::join('journal_entries', 'journal_details.journal_entry_id', '=', 'journal_entries.id')
    //             ->join('chart_of_accounts', 'journal_details.coa_id', '=', 'chart_of_accounts.id');

    //         if ($period == 'monthly') {
    //             $baseQuery->whereMonth('journal_entries.entry_date', $parsedDate->month)
    //                 ->whereYear('journal_entries.entry_date', $parsedDate->year);
    //         } elseif ($period == 'yearly') {
    //             $baseQuery->whereYear('journal_entries.entry_date', $parsedDate->year);
    //         }

    //         // Pendapatan Kotor (Gross Revenue)
    //         $grossRevenuesData = (clone $baseQuery)->where('chart_of_accounts.type', 'revenue')
    //             ->selectRaw('chart_of_accounts.name, chart_of_accounts.code, SUM(journal_details.credit) as amount') // Pastikan SUM dari tabel yang benar
    //             ->groupBy('chart_of_accounts.name', 'chart_of_accounts.code')
    //             ->orderBy('chart_of_accounts.code')
    //             ->get();
    //         $totalGrossRevenue = $grossRevenuesData->sum('amount');

    //         // Akun Kontra-Pendapatan (misalnya, Diskon Penjualan, Retur Penjualan)
    //         $contraRevenuesData = (clone $baseQuery)->where('chart_of_accounts.type', 'contra_revenue')
    //             ->selectRaw('chart_of_accounts.name, chart_of_accounts.code, SUM(journal_details.debit) as amount') // Kontra-akun biasanya di-sum dari sisi debit
    //             ->groupBy('chart_of_accounts.name', 'chart_of_accounts.code')
    //             ->orderBy('chart_of_accounts.code')
    //             ->get();
    //         $totalContraRevenue = $contraRevenuesData->sum('amount');

    //         // Pendapatan Bersih
    //         $totalNetRevenue = $totalGrossRevenue - $totalContraRevenue;

    //         // HPP (Gunakan kategori jika memungkinkan, bukan LIKE)
    //         // Misal, jika Anda sudah mengubah 'chart_of_accounts' untuk punya kolom 'category'
    //         // $hpp = (clone $baseQuery)->where('chart_of_accounts.category', 'COGS') 
    //         $hppData = (clone $baseQuery)->where('chart_of_accounts.name', 'LIKE', '%HPP%') // Sementara tetap pakai ini jika belum diubah
    //             ->selectRaw('chart_of_accounts.name, chart_of_accounts.code, SUM(journal_details.debit) as amount')
    //             ->groupBy('chart_of_accounts.name', 'chart_of_accounts.code')
    //             ->orderBy('chart_of_accounts.code')
    //             ->get();
    //         $totalHpp = $hppData->sum('amount');

    //         // Beban Operasional
    //         // Misal, jika HPP sudah pakai kategori COGS, maka 'expense' tidak perlu NOT LIKE HPP
    //         $operatingExpensesData = (clone $baseQuery)->where('chart_of_accounts.type', 'expense')
    //             ->where(function ($query) { // Tambahkan kondisi untuk mengecualikan akun HPP dari 'expense' jika masih pakai LIKE
    //                 $query->where('chart_of_accounts.name', 'NOT LIKE', '%HPP%')
    //                     ->orWhereNull('chart_of_accounts.name'); // Atau jika nama bisa null dan tetap tipe expense
    //             })
    //             ->selectRaw('chart_of_accounts.name, chart_of_accounts.code, SUM(journal_details.debit) as amount')
    //             ->groupBy('chart_of_accounts.name', 'chart_of_accounts.code')
    //             ->orderBy('chart_of_accounts.code')
    //             ->get();
    //         $totalOperatingExpenses = $operatingExpensesData->sum('amount');

    //         // Laba Kotor
    //         $grossProfit = $totalNetRevenue - $totalHpp; // Gunakan Pendapatan Bersih

    //         // Laba Bersih
    //         $netIncome = $grossProfit - $totalOperatingExpenses;

    //         return view('dashboard.reports.income_statement', compact(
    //             'grossRevenuesData',        // Ganti nama variabel agar jelas
    //             'totalGrossRevenue',
    //             'contraRevenuesData',       // Kirim data kontra-pendapatan
    //             'totalContraRevenue',
    //             'totalNetRevenue',          // Kirim pendapatan bersih
    //             'hppData',                  // Ganti nama variabel
    //             'totalHpp',
    //             'grossProfit',
    //             'operatingExpensesData',    // Ganti nama variabel
    //             'totalOperatingExpenses',
    //             'netIncome',
    //             'period',
    //             'dateInput'                 // Kirim tanggal input awal
    //         ));
    //     } catch (\Exception $e) {
    //         // \Log::error('Error generating income statement: ' . $e->getMessage() . "\n" . $e->getTraceAsString()); // Log error lebih detail
    //         return redirect()->back()->with('error', 'Gagal memuat laporan: ' . $e->getMessage());
    //     }
    // }


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

    // public function cashFlow(Request $request)
    // {
    //     $request->validate([
    //         'period' => 'sometimes|in:monthly,yearly',
    //         'date' => 'sometimes|date'
    //     ]);

    //     $period = $request->get('period', 'monthly');
    //     $date = $request->get('date', Carbon::now()->format('Y-m-d'));

    //     try {
    //         $query = JournalDetail::join('journal_entries', 'journal_details.journal_entry_id', '=', 'journal_entries.id')
    //             ->join('chart_of_accounts', 'journal_details.coa_id', '=', 'chart_of_accounts.id');

    //         if ($period == 'monthly') {
    //             $query->whereMonth('journal_entries.entry_date', Carbon::parse($date)->month)
    //                 ->whereYear('journal_entries.entry_date', Carbon::parse($date)->year);
    //         } elseif ($period == 'yearly') {
    //             $query->whereYear('journal_entries.entry_date', Carbon::parse($date)->year);
    //         }

    //         // Saldo awal kas
    //         $beginningCashQuery = JournalDetail::join('journal_entries', 'journal_details.journal_entry_id', '=', 'journal_entries.id')
    //             ->join('chart_of_accounts', 'journal_details.coa_id', '=', 'chart_of_accounts.id')
    //             ->where('chart_of_accounts.type', 'asset')
    //             ->where('chart_of_accounts.name', 'LIKE', '%Kas%');

    //         if ($period == 'monthly') {
    //             $beginningCashQuery->where('journal_entries.entry_date', '<', Carbon::parse($date)->startOfMonth());
    //         } else {
    //             $beginningCashQuery->where('journal_entries.entry_date', '<', Carbon::parse($date)->startOfYear());
    //         }

    //         $beginningCashBalance = $beginningCashQuery->sum('debit') - $beginningCashQuery->sum('credit');

    //         // Arus Kas dari Aktivitas Operasional
    //         $operatingActivities = (clone $query)
    //             ->where(function ($q) {
    //                 $q->where('chart_of_accounts.type', 'revenue')
    //                     ->orWhere('chart_of_accounts.type', 'expense')
    //                     ->orWhere('chart_of_accounts.name', 'LIKE', '%Piutang%')
    //                     ->orWhere('chart_of_accounts.name', 'LIKE', '%Utang%');
    //             })
    //             ->selectRaw('chart_of_accounts.name, chart_of_accounts.code, 
    //             SUM(credit) as cash_in, 
    //             SUM(debit) as cash_out')
    //             ->groupBy('chart_of_accounts.name', 'chart_of_accounts.code')
    //             ->orderBy('chart_of_accounts.code')
    //             ->get();

    //         $operatingCashIn = $operatingActivities->sum('cash_in');
    //         $operatingCashOut = $operatingActivities->sum('cash_out');

    //         // Arus Kas dari Aktivitas Investasi
    //         $investmentActivities = (clone $query)
    //             ->where(function ($q) {
    //                 $q->where('chart_of_accounts.name', 'LIKE', '%Aktiva Tetap%')
    //                     ->orWhere('chart_of_accounts.name', 'LIKE', '%Investasi%')
    //                     ->orWhere('chart_of_accounts.name', 'LIKE', '%Peralatan%');
    //             })
    //             ->selectRaw('chart_of_accounts.name, chart_of_accounts.code, 
    //             SUM(credit) as cash_in, 
    //             SUM(debit) as cash_out')
    //             ->groupBy('chart_of_accounts.name', 'chart_of_accounts.code')
    //             ->orderBy('chart_of_accounts.code')
    //             ->get();

    //         $investmentCashIn = $investmentActivities->sum('cash_in');
    //         $investmentCashOut = $investmentActivities->sum('cash_out');

    //         // Arus Kas dari Aktivitas Pendanaan
    //         $financingActivities = (clone $query)
    //             ->where(function ($q) {
    //                 $q->where('chart_of_accounts.type', 'equity')
    //                     ->orWhere('chart_of_accounts.type', 'liability')
    //                     ->where('chart_of_accounts.name', 'NOT LIKE', '%Utang Usaha%');
    //             })
    //             ->selectRaw('chart_of_accounts.name, chart_of_accounts.code, 
    //             SUM(credit) as cash_in, 
    //             SUM(debit) as cash_out')
    //             ->groupBy('chart_of_accounts.name', 'chart_of_accounts.code')
    //             ->orderBy('chart_of_accounts.code')
    //             ->get();

    //         $financingCashIn = $financingActivities->sum('cash_in');
    //         $financingCashOut = $financingActivities->sum('cash_out');

    //         // Perhitungan total
    //         $netOperating = $operatingCashIn - $operatingCashOut;
    //         $netInvesting = $investmentCashIn - $investmentCashOut;
    //         $netFinancing = $financingCashIn - $financingCashOut;
    //         $netCashFlow = $netOperating + $netInvesting + $netFinancing;
    //         $endingCashBalance = $beginningCashBalance + $netCashFlow;

    //         return view('dashboard.reports.cash_flow', compact(
    //             'beginningCashBalance',
    //             'operatingActivities',
    //             'netOperating',
    //             'investmentActivities',
    //             'netInvesting',
    //             'financingActivities',
    //             'netFinancing',
    //             'netCashFlow',
    //             'endingCashBalance',
    //             'period',
    //             'date'
    //         ));
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Gagal memuat laporan: ' . $e->getMessage());
    //     }
    // }
}
