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

                $data['tipeProsedur'] = DB::table('medical_record_procedure as mrp')
                    ->join('medical_records as mr', 'mr.id', '=', 'mrp.medical_record_id')
                    ->join('procedures as p', 'p.id', '=', 'mrp.procedure_id')
                    ->join('procedure_types as pt', 'pt.id', '=', 'p.procedure_type_id')
                    ->whereRaw("DATE_FORMAT(mr.tanggal_reservasi, '%Y-%m') = ?", [$bulanIni])
                    ->select(
                        'pt.name as procedure_type',
                        DB::raw('COUNT(DISTINCT mr.patient_id) as total_patients'),
                        DB::raw('COUNT(mrp.id) as total_procedures'),
                        DB::raw('GROUP_CONCAT(DISTINCT mr.id ORDER BY mr.id ASC SEPARATOR ", ") as medical_record_refs')
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
                $data['pasienAkanDatang'] = MedicalRecord::whereDate('tanggal_reservasi', $today)->count();
                $data['rekamMedisBelumDiisi'] = MedicalRecord::with('patient')
                    ->whereNull('teeth_condition')
                    ->orderBy('tanggal_reservasi', 'asc')
                    ->count();
                $data['listRekamMedisBelumDiisi'] = MedicalRecord::with(['patient', 'doctor'])
                    ->whereNull('teeth_condition')
                    ->orDoesntHave('procedures')
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

            // Redirect ke halaman 500 custom
            abort(500, 'Something went wrong');
        }
    }
}
