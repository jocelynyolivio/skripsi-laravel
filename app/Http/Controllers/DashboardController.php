<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Patient;
use App\Models\StockCard;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\MedicalRecord;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        $today = Carbon::today();
        $user = Auth::user();
        $role = $user->role->role_name; // Ambil role user

        $data = [
            'title' => 'Dashboard',
            'user' => $user,
            'role' => $role,
        ];

        // if ($role === 'manager') {
        //     $data['jumlahPasienHariIni'] = MedicalRecord::whereDate('tanggal_reservasi', $today)->count();
        //     $data['pendapatanHariIni'] = Transaction::whereDate('created_at', $today)->sum('total_amount');
        //     $data['jumlahKunjunganBulanIni'] = MedicalRecord::whereMonth('tanggal_reservasi', $today->month)->count();
        // } 
        if ($role === 'manager') {
            // **Data Kunjungan Pasien & Omzet Hari Ini**
            $data['jumlahPasienHariIni'] = MedicalRecord::whereDate('tanggal_reservasi', $today)->count();
            $data['pendapatanHariIni'] = Transaction::whereDate('created_at', $today)->where('status', 'lunas')
                ->sum('total_amount');

            // **Jumlah Rata-rata Kunjungan Pasien dalam 1 Bulan (Grafik)**
            $data['kunjunganBulanan'] = MedicalRecord::selectRaw('DATE(tanggal_reservasi) as date, COUNT(*) as jumlah')
                ->whereMonth('tanggal_reservasi', $today->month)
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $data['purchaseRequestBelumApprove'] = PurchaseRequest::whereNull('approved_at')->count();;

            // **Filter Data Pasien Berdasarkan Request**
            $query = Patient::query();

            // **Filter Berdasarkan Usia**
            if ($request->filled('usia_min') && $request->filled('usia_max')) {
                $minDate = Carbon::today()->subYears($request->usia_max)->format('Y-m-d');
                $maxDate = Carbon::today()->subYears($request->usia_min)->format('Y-m-d');

                $query->whereBetween('date_of_birth', [$minDate, $maxDate]);
            }

            // **Filter Berdasarkan Jenis Kelamin**
            if ($request->filled('jenis_kelamin')) {
                $query->where('gender', $request->jenis_kelamin);
            }

            // **Filter Berdasarkan Domisili (Kota)**
            if ($request->filled('domisili')) {
                $query->where('home_city', 'LIKE', "%{$request->domisili}%");
            }

            // **Filter Berdasarkan Jasa (Jika ada relasi dengan `procedures`)**
            if ($request->filled('jasa')) {
                $query->whereHas('procedures', function ($q) use ($request) {
                    $q->where('nama', 'LIKE', "%{$request->jasa}%");
                });
            }

            $data['filteredPatients'] = $query->get();
        } elseif ($role === 'admin') {
            $data['pasienAkanDatang'] = MedicalRecord::whereBetween('tanggal_reservasi', [$today, $today->copy()->endOfWeek()])->count();

            $data['pasienPerluReminder'] = MedicalRecord::whereNull('status_konfirmasi')->count(); // Hanya hitung jumlah

            $data['pasienReminderList'] = MedicalRecord::with(['patient', 'doctor']) // Ambil data pasien yang perlu diingatkan
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
        }

        // Check if it's an AJAX request
        if ($request->ajax()) {
            return response()->json($data);  // Return JSON response for AJAX request
        }

        // Return the view if it's not an AJAX request
        return view('dashboard.index', $data);
    }

    // public function index()
    // {
    //     $today = Carbon::today();

    //     // Hitung jumlah pasien hari ini
    //     $jumlahPasienHariIni = MedicalRecord::whereDate('tanggal_reservasi', $today)->count();

    //     // Hitung jumlah reservasi yang belum diproses
    //     $reservasiBelumDiproses = MedicalRecord::whereNull('status_konfirmasi')->count();

    //     // Hitung total pendapatan hari ini
    //     $pendapatanHariIni = Transaction::whereDate('created_at', Carbon::today())->sum('total_amount');

    //     // puiutang
    //     // $transaksiBelumLunas = Transaction::where('status','belum lunas')->value('total_amount');
    //     // $transaksiBelumLunas = Payment::groupBy('transaction_id')->sum('amount');

    //     // $piutang = Transaction::where('status', 'belum lunas')
    //     //     ->with('payments')
    //     //     ->get();

    //     // // Total Semua Piutang (Belum Lunas)
    //     // $transaksiBelumLunas = $piutang->sum('remaining_amount');

    //     $transaksiBelumLunas = Receivable::sum('remaining_amount');
    //     // dd($transaksiBelumLunas);


    //     $user = Auth::user();
    //     return view('dashboard.index', [
    //         'title' => 'Dashboard',
    //         'user' => $user,
    //         'role' => $user->role->role_name,
    //         'jumlahPasienHariIni' => $jumlahPasienHariIni,
    //         'reservasiBelumDiproses' => $reservasiBelumDiproses,
    //         'pendapatanHariIni' => $pendapatanHariIni,
    //         'transaksiBelumLunas' => $transaksiBelumLunas,
    //     ]);
    // }
}
