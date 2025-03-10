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
        $period = $request->get('period', 'monthly'); // Default bulanan
        $date = $request->get('date', Carbon::now()->format('Y-m-d'));

        $query = JournalDetail::selectRaw(
            '
            chart_of_accounts.id as coa_id, 
            chart_of_accounts.name as coa_name, 
            chart_of_accounts.type, 
            SUM(journal_details.debit) as total_debit, 
            SUM(journal_details.credit) as total_credit'
        )
            ->join('journal_entries', 'journal_details.journal_entry_id', '=', 'journal_entries.id')
            ->join('chart_of_accounts', 'journal_details.coa_id', '=', 'chart_of_accounts.id')
            ->whereIn('chart_of_accounts.type', ['asset', 'liability', 'equity']); // Hanya akun Neraca

        if ($period == 'monthly') {
            $query->whereMonth('journal_entries.entry_date', Carbon::parse($date)->month)
                ->whereYear('journal_entries.entry_date', Carbon::parse($date)->year);
        } elseif ($period == 'yearly') {
            $query->whereYear('journal_entries.entry_date', Carbon::parse($date)->year);
        }

        $coaSummary = $query->groupBy('chart_of_accounts.id', 'chart_of_accounts.name', 'chart_of_accounts.type')
            ->get();

        // ini ngitung laba ditahan (retained earnings)
        // kalau laba rugi nya menunjukan rugi jadi laba ditahannya bertambah di debit (supaya nanti balance)
        $profitOrLoss = JournalDetail::selectRaw("
        SUM(journal_details.credit) - SUM(journal_details.debit) as net_income
    ")
    ->join('journal_entries', 'journal_details.journal_entry_id', '=', 'journal_entries.id')
    ->join('chart_of_accounts', 'journal_details.coa_id', '=', 'chart_of_accounts.id')
    ->whereIn('chart_of_accounts.type', ['revenue', 'expense']) // Hanya akun laba rugi
    ->whereDate('journal_entries.entry_date', '<=', $date)
    ->first();

// Jika ada laba/rugi, tambahkan ke laporan neraca
if ($profitOrLoss) {
    $netIncome = $profitOrLoss->net_income;

    // Jika laba positif, masuk ke Kredit di ekuitas
    // Jika rugi, masuk ke Debit di ekuitas
    $coaSummary->push((object)[
        'coa_id' => null,
        'coa_name' => 'Laba Ditahan (Setelah Laba/Rugi)',
        'type' => 'equity',
        'total_debit' => ($netIncome < 0) ? abs($netIncome) : 0,
        'total_credit' => ($netIncome > 0) ? $netIncome : 0,
    ]);
}

        return view('dashboard.reports.balance_sheet', compact('coaSummary', 'period', 'date'));
    }

    public function incomeStatement(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $date = $request->get('date', Carbon::now()->format('Y-m-d'));

        $query = JournalDetail::join('journal_entries', 'journal_details.journal_entry_id', '=', 'journal_entries.id')
            ->join('chart_of_accounts', 'journal_details.coa_id', '=', 'chart_of_accounts.id');

        if ($period == 'monthly') {
            $query->whereMonth('journal_entries.entry_date', Carbon::parse($date)->month)
                ->whereYear('journal_entries.entry_date', Carbon::parse($date)->year);
        } elseif ($period == 'yearly') {
            $query->whereYear('journal_entries.entry_date', Carbon::parse($date)->year);
        }

        $revenues = (clone $query)->where('chart_of_accounts.type', 'revenue')
            ->selectRaw('chart_of_accounts.name, SUM(credit) as saldo')
            ->groupBy('chart_of_accounts.name')
            ->get();

        $hpp = (clone $query)->where('chart_of_accounts.name', 'LIKE', '%HPP%')
            ->selectRaw('chart_of_accounts.name, SUM(debit) as saldo')
            ->groupBy('chart_of_accounts.name')
            ->get();

        $operatingExpenses = (clone $query)->where('chart_of_accounts.type', 'expense')
            ->where('chart_of_accounts.name', 'NOT LIKE', '%HPP%')
            ->selectRaw('chart_of_accounts.name, SUM(debit) as saldo')
            ->groupBy('chart_of_accounts.name')
            ->get();

        $netIncome = $revenues->sum('saldo') - $hpp->sum('saldo') - $operatingExpenses->sum('saldo');

        return view('dashboard.reports.income_statement', compact('revenues', 'hpp', 'operatingExpenses', 'netIncome', 'period', 'date'));
    }


    public function cashFlow(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $date = $request->get('date', Carbon::now()->format('Y-m-d'));

        $query = JournalDetail::join('journal_entries', 'journal_details.journal_entry_id', '=', 'journal_entries.id')
            ->join('chart_of_accounts', 'journal_details.coa_id', '=', 'chart_of_accounts.id');

        if ($period == 'monthly') {
            $query->whereMonth('journal_entries.entry_date', Carbon::parse($date)->month)
                ->whereYear('journal_entries.entry_date', Carbon::parse($date)->year);
        } elseif ($period == 'yearly') {
            $query->whereYear('journal_entries.entry_date', Carbon::parse($date)->year);
        }

        // Arus Kas dari Aktivitas Operasional
        $operatingCashIn = (clone $query)->where('chart_of_accounts.type', 'revenue')
            ->sum('credit');

        $operatingCashOut = (clone $query)->where('chart_of_accounts.type', 'expense')
            ->sum('debit');

        // Arus Kas dari Aktivitas Investasi
        $investmentCashIn = (clone $query)->where('chart_of_accounts.name', 'LIKE', '%Investasi%')
            ->sum('credit');

        $investmentCashOut = (clone $query)->where('chart_of_accounts.name', 'LIKE', '%Investasi%')
            ->sum('debit');

        // Arus Kas dari Aktivitas Pendanaan
        $financingCashIn = (clone $query)->where('chart_of_accounts.type', 'equity')
            ->sum('credit');

        $financingCashOut = (clone $query)->where('chart_of_accounts.type', 'liability')
            ->sum('debit');

        $netCashFlow = ($operatingCashIn - $operatingCashOut) + ($investmentCashIn - $investmentCashOut) + ($financingCashIn - $financingCashOut);

        return view('dashboard.reports.cash_flow', compact(
            'operatingCashIn',
            'operatingCashOut',
            'investmentCashIn',
            'investmentCashOut',
            'financingCashIn',
            'financingCashOut',
            'netCashFlow',
            'period',
            'date'
        ));
    }
}
