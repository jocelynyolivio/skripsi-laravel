<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Patient;
use App\Models\StockCard;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\MedicalRecord;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        try {
            $today = Carbon::today();
            $user = Auth::user();
            $role = $user->role->role_name;

            $data = [
                'title' => 'Dashboard',
                'user' => $user,
                'role' => $role,
            ];
            if ($role === 'manager') {
                $data['jumlahPasienHariIni'] = MedicalRecord::whereDate('tanggal_reservasi', $today)->count();
                $data['pendapatanHariIni'] = Transaction::whereDate('created_at', $today)->where('status', 'lunas')
                    ->sum('total_amount');

                $data['kunjunganBulanan'] = MedicalRecord::selectRaw('DATE(tanggal_reservasi) as date, COUNT(*) as jumlah')
                    ->whereMonth('tanggal_reservasi', $today->month)
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();

                $data['purchaseRequestBelumApprove'] = PurchaseRequest::whereNull('approved_at')->count();;

                $data['performaDokter'] = Transaction::selectRaw('
                    transactions.doctor_id,
                    users.name as doctor_name,
                    SUM(transactions.total_amount) as total_amount')
                    ->join('users', 'transactions.doctor_id', '=', 'users.id')
                    ->groupBy('transactions.doctor_id', 'users.name')
                    ->get();

                $today = now();
                $bulanIni = $today->format('Y-m');

                $subTotalPerTransaction = DB::table('transaction_items')
                    ->select('transaction_id', DB::raw('SUM(final_price) as total_per_transaction'))
                    ->groupBy('transaction_id');

                $data['statistik'] = DB::table('transaction_items as ti')
                    ->join('transactions as t', 't.id', '=', 'ti.transaction_id')
                    ->joinSub($subTotalPerTransaction, 'tt', function ($join) {
                        $join->on('tt.transaction_id', '=', 'ti.transaction_id');
                    })
                    ->join('payments as pay', 'pay.transaction_id', '=', 't.id')
                    ->join('medical_records as mr', 'mr.id', '=', 't.medical_record_id')
                    ->join('patients as p', 'p.id', '=', 'mr.patient_id')
                    ->join('procedures as proc', 'proc.id', '=', 'ti.procedure_id')
                    ->join('procedure_types as pt', 'pt.id', '=', 'proc.procedure_type_id')
                    ->whereRaw("DATE_FORMAT(mr.tanggal_reservasi, '%Y-%m') = ?", [$bulanIni])
                    ->select(
                        'pt.name as procedure_type',
                        DB::raw('COUNT(DISTINCT mr.patient_id) as total_patients'),
                        DB::raw("COUNT(DISTINCT CASE WHEN p.gender = 'male' THEN mr.patient_id END) as male_patients"),
                        DB::raw("COUNT(DISTINCT CASE WHEN p.gender = 'female' THEN mr.patient_id END) as female_patients"),
                        DB::raw('SUM((ti.final_price / tt.total_per_transaction) * pay.amount) as total_revenue'),
                        DB::raw("SUM(CASE WHEN p.gender = 'male' THEN (ti.final_price / tt.total_per_transaction) * pay.amount ELSE 0 END) as male_revenue"),
                        DB::raw("SUM(CASE WHEN p.gender = 'female' THEN (ti.final_price / tt.total_per_transaction) * pay.amount ELSE 0 END) as female_revenue")
                    )
                    ->groupBy('pt.id', 'pt.name')
                    ->get();
            } elseif ($role === 'admin') {
                $data['pasienAkanDatang'] = MedicalRecord::whereBetween('tanggal_reservasi', [$today, $today->copy()->endOfWeek()])->count();

                $data['pasienUltah'] = Patient::where('date_of_birth', $today)->count();
                $data['pasienPerluReminder'] = MedicalRecord::whereNull('status_konfirmasi')->count();
                $data['pasienReminderList'] = MedicalRecord::with(['patient', 'doctor'])
                    ->whereNull('status_konfirmasi')
                    ->orderBy('tanggal_reservasi', 'asc')
                    ->get()
                    ->map(function ($item) {
                        $item->whatsapp_url = route('dashboard.reservations.whatsapp', $item->id);
                        $item->whatsapp_confirm_url = route('dashboard.reservations.whatsappConfirm', $item->id);
                        return $item;
                    });
                $data['lowStockItems'] = StockCard::with('dentalMaterial')
                    ->where('remaining_stock', '<', 10)
                    ->groupBy('dental_material_id')
                    ->selectRaw('MAX(id) as id')
                    ->get()
                    ->map(function ($item) {
                        return StockCard::with('dentalMaterial')->find($item->id);
                    });
                $data['performaDokter'] = Transaction::selectRaw('
                    transactions.doctor_id,
                    users.name as doctor_name,
                    SUM(transactions.total_amount) as total_amount')
                    ->join('users', 'transactions.doctor_id', '=', 'users.id')
                    ->groupBy('transactions.doctor_id', 'users.name')
                    ->get();
            } elseif ($role === 'dokter tetap') {
                $data['pasienAkanDatang'] = MedicalRecord::where('doctor_id', $user->id)
                    ->whereDate('tanggal_reservasi', $today)
                    ->count();
                $data['rekamMedisBelumDiisi'] = MedicalRecord::where('doctor_id', $user->id)
                    ->whereNull('teeth_condition')
                    ->count();
                $data['listRekamMedisBelumDiisi'] = MedicalRecord::with(['patient', 'doctor'])
                    ->where('doctor_id', $user->id) // Filter dokter yang login dulu
                    ->where(function ($query) {    // Kelompokkan kondisi "belum diisi"
                        $query->whereNull('teeth_condition')
                            ->orDoesntHave('procedures');
                    })
                    ->orderBy('tanggal_reservasi', 'asc')
                    ->get();

                $data['performaDokter'] = Transaction::selectRaw('
                    transactions.doctor_id,
                    users.name as doctor_name,
                    SUM(transactions.total_amount) as total_amount')
                    ->join('users', 'transactions.doctor_id', '=', 'users.id')
                    ->groupBy('transactions.doctor_id', 'users.name')
                    ->get();
            }

            // Check if it's an AJAX request
            if ($request->ajax()) {
                return response()->json($data);  // Return JSON response for AJAX request
            }

            return view('dashboard.index', $data);
        } catch (\Exception $e) {
            // dd($e);
            // Redirect ke halaman 500 custom
            abort(500, 'Something went wrong');
        }
    }
}
