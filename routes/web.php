<?php

use App\Models\HomeContent;
use Illuminate\Http\Request;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\OdontogramController;
use App\Http\Controllers\HomeContentController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PatientLoginController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\DentalMaterialController;
use App\Http\Controllers\ExpenseRequestController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\ScheduleOverrideController;
use App\Http\Controllers\ScheduleTemplateController;
use App\Http\Controllers\ProcedureMaterialController;
use App\Http\Controllers\SalaryCalculationController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $contents = HomeContent::all();
    return view('home', [
        "title" => "SenyumQu",
        "active" => "home",
        "contents" => $contents,
    ]);
});

Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/patient/login', [PatientLoginController::class, 'showLoginForm'])->name('patient.login');
Route::post('/patient/login', [PatientLoginController::class, 'login']);
Route::post('/patient/logout', [PatientLoginController::class, 'logout'])->name('patient.logout');

Route::get('/email/verify', function () {
    return view('auth.verify-email', [
        'title' => 'Email Verification'
    ]);
})->middleware('auth:patient')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/'); // Redirect ke halaman setelah verifikasi
})->middleware(['auth:patient', 'signed'])->name('verification.verify');

Route::post('/email/resend', function (Request $request) {
    $request->user('patient')->sendEmailVerificationNotification();
    return back()->with('message', 'Verification email sent!');
})->middleware(['auth:patient', 'throttle:6,1'])->name('verification.resend');

Route::get('/patient/register', [RegisterController::class, 'index'])->name('register')->middleware('guest');
// kalo ada req ke halaman register tapi method post maka nanti akan panggil yg store
Route::post('/patient/register', [RegisterController::class, 'store']);

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('internal')
    ->name('dashboard');

Route::get('/reservation', [ReservationController::class, 'index'])
    ->name('reservation.index')
    ->middleware(['auth:patient', 'verified']);
Route::get('/reservation/upcoming', [ReservationController::class, 'upcomingReservations'])
    ->name('reservations.upcoming')
    ->middleware(['auth:patient', 'verified']); // Pastikan hanya pasien yang login yang bisa melihat
Route::post('/reservation', [ReservationController::class, 'store'])->name('reservation.store')->middleware(['auth:patient', 'verified']);

// isi dashboarrrddddd
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/{user}', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/salaries/upload-salary', [SalaryController::class, 'uploadForm']);
    Route::post('/salaries/process-salary', [SalaryController::class, 'processExcel']);
    Route::get('/salaries', [SalaryController::class, 'index'])->name('salaries.index');
    Route::get('/salaries/slips', [SalaryController::class, 'slips'])->name('salaries.slips');
    Route::get('/salaries/slip', [SalaryController::class, 'userSalarySlip'])->name('salaries.slip');
    Route::post('/salaries/process', [SalaryController::class, 'processSalaries'])->name('salaries.process');
    Route::post('/salaries/calculate', [SalaryController::class, 'calculateSalaries'])->name('salaries.calculate');
    Route::post('/salaries/doctors', [SalaryController::class, 'calculateDoctorSalaries'])->name('salaries.doctor');
    Route::post('/salaries/store', [SalaryController::class, 'storeSalaries'])->name('salaries.store');
    Route::post('/salaries/storeDoctor', [SalaryController::class, 'storeDoctorSalaries'])->name('salaries.storeDoctor');
    Route::get('/salaries', [SalaryController::class, 'index'])->name('salaries.index');
    Route::get('/salaries/data', [SalaryController::class, 'getSalaryData'])->name('salaries.data');

    Route::get('/home_content/{homeContent}/edit', [HomeContentController::class, 'edit'])->name('home_content.edit');
    Route::resource('/home_content', HomeContentController::class);

    Route::get('/masters/patients/{id}/edit', [PatientController::class, 'edit'])->name('masters.patients.edit');
    Route::put('/masters/patients/{id}', [PatientController::class, 'update'])->name('masters.patients.update');
    Route::delete('/masters/patients/{id}', [PatientController::class, 'destroy'])->name('masters.patients.destroy');
    Route::get('/masters/patients', [PatientController::class, 'index'])->name('masters.patients');
    Route::get('/masters/patients/create', [PatientController::class, 'create'])->name('masters.patients.create');
    Route::post('/masters/patients', [PatientController::class, 'store'])->name('masters.patients.store');

    Route::get('/masters', [UserController::class, 'index'])->name('masters.index');
    Route::get('/masters/{id}/edit', [UserController::class, 'edit'])->name('masters.edit');
    Route::put('/masters/{id}', [UserController::class, 'update'])->name('masters.update');
    Route::delete('/masters/{id}', [UserController::class, 'destroy'])->name('masters.destroy');

    // Route::resource('/schedules/templates', ScheduleTemplateController::class);
    // Route::resource('/schedules/overrides', ScheduleOverrideController::class)->except(['show']);

    Route::prefix('schedules')->name('schedules.')->group(function () {
        Route::resource('templates', ScheduleTemplateController::class);
        Route::resource('overrides', ScheduleOverrideController::class)->except(['show']);
    });

    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::get('/schedules/get-doctors-by-date', [ScheduleController::class, 'getDoctorsByDate'])
        ->name('schedules.get-doctors-by-date');
    Route::get('/schedules/get-patients', [ScheduleController::class, 'getPatients'])->name('schedules.get-patients');
    Route::post('/schedules/store-reservation', [ScheduleController::class, 'storeReservation'])->name('schedules.store-reservation');

    Route::post('/reservations', [ScheduleController::class, 'storeReservation'])->name('reservations.store');

    // pppppppp
    Route::get('/reservations', [MedicalRecordController::class, 'list'])->name('reservations.index');

    Route::get('/reservations/{reservation}/edit', [ScheduleController::class, 'editReservation'])->name('reservations.edit');
    Route::put('/reservations/{reservation}', [ScheduleController::class, 'updateReservation'])->name('reservations.update');
    Route::delete('/reservations/{reservation}', [MedicalRecordController::class, 'destroyReservation'])->name('reservations.destroy');
    Route::get('/reservations/get-available-times', [ScheduleController::class, 'getAvailableTimes'])->name('reservations.getAvailableTimes');

    // ppppp
    Route::get('/reservations/whatsapp/{id}', [MedicalRecordController::class, 'sendWhatsApp'])->name('reservations.whatsapp');
    Route::get('/reservations/whatsappConfirm/{id}', [MedicalRecordController::class, 'waConfirmation'])->name('reservations.whatsappConfirm');

    Route::prefix('patients/{patientId}')->group(function () {
        // Route to list all medical records for a patient
        Route::get('/medical_records', [MedicalRecordController::class, 'index'])->name('medical_records.index');
        // Route to display the form for creating a new medical record
        Route::get('/medical_records/create', [MedicalRecordController::class, 'create'])->name('medical_records.create');
        // Route to store a new medical record
        Route::post('/medical_records', [MedicalRecordController::class, 'store'])->name('medical_records.store');
        // Route to show the edit form for an existing medical record
        Route::get('/medical_records/{recordId}/edit', [MedicalRecordController::class, 'edit'])->name('medical_records.edit');
        // Route to update an existing medical record
        Route::put('/medical_records/{recordId}', [MedicalRecordController::class, 'update'])->name('medical_records.update');
        // Route to delete a medical record
        Route::delete('/medical_records/{recordId}', [MedicalRecordController::class, 'destroy'])->name('medical_records.destroy');
    });
    Route::get('/medical_records/{medicalRecordId}/selectMaterials', [MedicalRecordController::class, 'selectMaterials'])
        ->name('medical_records.selectMaterials');
    Route::post('/medical_records/{medicalRecordId}/saveMaterials', [MedicalRecordController::class, 'saveMaterials'])
        ->name('medical_records.saveMaterials');

    Route::resource('/dental-materials', DentalMaterialController::class);

    Route::resource('procedure_materials', ProcedureMaterialController::class);

    Route::post('/transactions/storeWithPayment', [TransactionController::class, 'storeWithPayment'])->name('transactions.storeWithPayment');

    Route::get('/transactions/select-medical-record', [MedicalRecordController::class, 'selectForTransaction'])
        ->name('transactions.selectMedicalRecord');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions/{transactionId}/pay-remaining', [TransactionController::class, 'payRemaining'])->name('transactions.payRemaining');


    // Route untuk transaksi dengan rekam medis
    Route::get('/transactions/create/{medicalRecordId}', [TransactionController::class, 'create'])
        ->name('transactions.create');
    // Route untuk transaksi tanpa rekam medis
    Route::get('/transactions/create', [TransactionController::class, 'createWithoutMedicalRecord'])
        ->name('transactions.createWithoutMedicalRecord');

    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::post('/transactions/store-without-medical-record', [TransactionController::class, 'storeWithoutMedicalRecord'])->name('transactions.storeWithoutMedicalRecord');

    Route::get('/transactions/{id}/struk', [TransactionController::class, 'showStruk'])->name('transactions.showStruk');

    Route::resource('expenses', ExpenseController::class);

    Route::resource('expense_requests', ExpenseRequestController::class);
    Route::patch('expense_requests/{id}/approve', [ExpenseRequestController::class, 'approve'])->name('expense_requests.approve');
    Route::patch('expense_requests/{id}/reject', [ExpenseRequestController::class, 'reject'])->name('expense_requests.reject');
    Route::patch('expense_requests/{id}/done', [ExpenseRequestController::class, 'markDone'])->name('expense_requests.done');

    // Route::resource('attendances', AttendanceController::class);

    Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('attendances/create', [AttendanceController::class, 'create'])->name('attendances.create');
    Route::post('attendances/store', [AttendanceController::class, 'store'])->name('attendances.store');
    Route::get('attendances/{attendance}', [AttendanceController::class, 'show'])->name('attendances.show');
    Route::get('attendances/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendances.edit');
    Route::put('attendances/{attendance}', [AttendanceController::class, 'update'])->name('attendances.update');
    Route::delete('attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');

    Route::resource('holidays', HolidayController::class);

    Route::prefix('odontograms')->name('odontograms.')->group(function () {
        Route::get('/{patientId}', [OdontogramController::class, 'index'])->name('index');
        Route::post('/{patientId}', [OdontogramController::class, 'store'])->name('store');
    });

    Route::resource('coa', ChartOfAccountController::class);

    Route::get('/journals', [JournalController::class, 'index'])->name('journals.index');
    Route::get('/journals/show/{id}', [JournalController::class, 'show'])->name('journals.show');

    Route::resource('salary_calculations', SalaryCalculationController::class);

    Route::resource('suppliers', SupplierController::class);

    Route::post('/purchases/{purchase}/pay', [ExpenseController::class, 'payDebt'])->name('purchases.pay');

    Route::resource('purchases', PurchaseController::class);

    Route::get('/purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
    Route::post('/purchases/{purchase}/receive', [PurchaseController::class, 'storeReceived'])->name('purchases.storeReceived');

    Route::get('/purchases/{purchase}/pay', [PurchaseController::class, 'showPayForm'])->name('purchases.pay');
    Route::post('/purchases/pay', [PurchaseController::class, 'payDebt'])->name('purchases.payDebt');

    Route::get('/reports/balance_sheet', [FinancialReportController::class, 'balanceSheet'])->name('reports.balance_sheet');
    Route::get('/reports/income_statement', [FinancialReportController::class, 'incomeStatement'])->name('reports.income_statement');
    Route::get('/reports/cash_flow', [FinancialReportController::class, 'cashFlow'])->name('reports.cash_flow');
});
