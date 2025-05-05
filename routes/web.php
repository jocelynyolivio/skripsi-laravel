<?php

use App\Models\Patient;
use App\Models\HomeContent;
use Illuminate\Http\Request;
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
use App\Http\Controllers\ProcedureController;
use App\Http\Controllers\StockCardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\OdontogramController;
use App\Http\Controllers\HomeContentController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PatientLoginController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\ProcedureTypeController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\DentalMaterialController;
use App\Http\Controllers\ExpenseRequestController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\PurchasePaymentController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\ScheduleOverrideController;
use App\Http\Controllers\ScheduleTemplateController;
use App\Http\Controllers\ProcedureMaterialController;
use App\Http\Controllers\SalaryCalculationController;
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
})->name('index');

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

// Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//     dd("Verifikasi Route Berjalan!");

//     $request->fulfill();
//     return redirect('/patient/login'); // Redirect ke halaman setelah verifikasi
// })->middleware(['auth:patient', 'signed'])->name('verification.verify');
// Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//     $request->fulfill();

//     dd(auth('patient')->user()); // Debug: Cek apakah user terautentikasi dengan guard patient

//     return redirect('/patient/login')->with('success', 'Email verified successfully. Please log in.');
// })->middleware(['auth:patient', 'signed'])->name('verification.verify');

// Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//     $request->fulfill();

//     dd("Email Berhasil Diverifikasi!", auth('patient')->user());

//     return redirect('/patient/login')->with('success', 'Email verified successfully. Please log in.');
// })->middleware(['auth:patient', 'signed'])->name('verification.verify');

// Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//     $patient = $request->user();

//     if (!$patient) {
//         return redirect('/patient/login')->with('error', 'You need to login first.');
//     }

//     if (!$patient->hasVerifiedEmail()) {
//         $patient->email_verified_at = now(); // Paksa update
//         $patient->save();
//     }

//     return redirect('/patient/login')->with('success', 'Email verified successfully. Please log in.');
// })->middleware(['auth:patient', 'signed'])->name('verification.verify');

// Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//     dd("Route Verifikasi Berjalan!");
// });

// Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
//     $patient = Patient::find($id);

//     // Cek apakah pasien ditemukan
//     if (!$patient) {
//         dd("ERROR: Pasien tidak ditemukan!", $id);
//     }

//     // Cek apakah email sudah diverifikasi sebelumnya
//     if ($patient->hasVerifiedEmail()) {
//         dd("Email sudah diverifikasi sebelumnya!", $patient);
//     }

//     // Coba update email_verified_at
//     $patient->email_verified_at = now();
//     $saveResult = $patient->save(); // Simpan perubahan ke database

//     // Debug apakah save berhasil
//     // dd("Hasil Update:", $saveResult, "Data Pasien Setelah Update:", $patient);
//     return redirect('/patient/login')->with('success', 'Email verified successfully. Please log in.');
// })->name('verification.verify');


Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $patient = Patient::find($id);

    // Cek apakah pasien ditemukan
    if (!$patient) {
        return redirect('/')->with('error', 'Patient not found. Please register or contact support.');
    }

    // Cek apakah email sudah diverifikasi sebelumnya
    if ($patient->hasVerifiedEmail()) {
        return redirect('/')->with('info', 'Email has already been verified. You can login now.');
    }

    // Coba update email_verified_at
    $patient->email_verified_at = now();
    $saveResult = $patient->save(); // Simpan perubahan ke database

    return redirect('/patient/login')->with('success', 'Email verified successfully. Please log in.');
})->name('verification.verify');

// Route::get('/email/verify/{id}/{hash}', PatientVerifyEmailController::class)
//     ->middleware(['auth:patient', 'signed'])
//     ->name('verification.verify');

Route::post('/email/resend', function (Request $request) {
    $request->user('patient')->sendEmailVerificationNotification();
    return back()->with('message', 'Verification email sent!');
})->middleware(['auth:patient', 'throttle:6,1'])->name('verification.resend');

Route::get('/patient/register', [RegisterController::class, 'index'])->name('patient.register')->middleware('guest');

// kalo ada req ke halaman register tapi method post maka nanti akan panggil yg store
Route::post('/patient/register', [RegisterController::class, 'store']);

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('internal')
    ->name('dashboard');

Route::get('/dashboard/data', [DashboardController::class, 'fetchData'])->name('dashboard.data');

Route::get('/reservation', [ReservationController::class, 'index'])
    ->name('reservation.index')
    ->middleware(['auth:patient', 'verified']);
Route::get('/reservation/upcoming', [ReservationController::class, 'upcomingReservations'])
    ->name('reservations.upcoming')
    ->middleware(['auth:patient', 'verified']); // Pastikan hanya pasien yang login yang bisa melihat
Route::post('/reservation', [ReservationController::class, 'store'])
    ->name('reservation.store')
    ->middleware(['auth:patient', 'verified']);

Route::get('dashboard/schedules/get-doctors-by-date', [ScheduleController::class, 'getDoctorsByDate'])
    ->name('dashboard.schedules.get-doctors-by-date');

// isi dashboarddddd
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::resource('purchase_orders', PurchaseOrderController::class);

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/{user}', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/salaries/upload-salary', [SalaryController::class, 'uploadForm'])->name('salaries.upload')->middleware('role:manager');
    Route::post('/salaries/process-salary', [SalaryController::class, 'processExcel'])->middleware('role:manager');
    Route::get('/salaries', [SalaryController::class, 'index'])->name('salaries.index')->middleware('role:manager');
    Route::post('/salaries/process', [SalaryController::class, 'processSalaries'])->name('salaries.process')->middleware('role:manager');
    Route::post('/salaries/calculate', [SalaryController::class, 'calculateSalaries'])->name('salaries.calculate')->middleware('role:manager');
    Route::post('/salaries/doctors', [SalaryController::class, 'calculateDoctorSalaries'])->name('salaries.doctor')->middleware('role:manager');
    Route::post('/salaries/store', [SalaryController::class, 'storeSalaries'])->name('salaries.store')->middleware('role:manager');
    Route::post('/salaries/storeDoctor', [SalaryController::class, 'storeDoctorSalaries'])->name('salaries.storeDoctor')->middleware('role:manager');
    Route::get('/salaries', [SalaryController::class, 'index'])->name('salaries.index')->middleware('role:manager');
    Route::get('/salaries/data', [SalaryController::class, 'getSalaryData'])->name('salaries.data')->middleware('role:manager');
    Route::post('/salaries/handle', [SalaryController::class, 'handleSalaries'])->name('salaries.store')->middleware('role:manager');

    // slip gaji masing"
    Route::get('/salaries/slips', [SalaryController::class, 'slips'])->name('salaries.slips');
    Route::get('/salaries/slip', [SalaryController::class, 'userSalarySlip'])->name('salaries.slip');

    Route::get('/home_content/{homeContent}/edit', [HomeContentController::class, 'edit'])->name('home_content.edit');
    Route::resource('/home_content', HomeContentController::class);

    Route::resource('/procedures', ProcedureController::class)->names('procedures');
    Route::resource('/procedure_types', ProcedureTypeController::class);

    Route::get('/masters/patients/{id}/edit', [PatientController::class, 'edit'])->name('masters.patients.edit');
    Route::put('/masters/patients/{id}', [PatientController::class, 'update'])->name('masters.patients.update');
    Route::delete('/masters/patients/{id}', [PatientController::class, 'destroy'])->name('masters.patients.destroy');
    Route::get('/masters/patients', [PatientController::class, 'index'])->name('masters.patients');
    Route::get('/masters/patients/create', [PatientController::class, 'create'])->name('masters.patients.create');
    Route::post('/masters/patients', [PatientController::class, 'store'])->name('masters.patients.store');
    Route::get('/masters/patients/patient-birthday', [PatientController::class, 'birthday'])->name('masters.patients.birthday');
    Route::get('/masters/patients/patient-birthday/{id}', [PatientController::class, 'sendVoucherBirthday'])->name('masters.patients.birthday.sendVoucherBirthday');
    Route::get('/masters/patients/patient-birthday-generate/{id}', [PatientController::class, 'generateVoucherBirthday'])->name('masters.patients.birthday.generateVoucherBirthday');

    Route::get('/masters', [UserController::class, 'index'])->name('masters.index');
    Route::get('/masters/create', [UserController::class, 'create'])->name('masters.create')->middleware('role:manager');
    Route::post('/masters', [UserController::class, 'store'])->name('masters.store')->middleware('role:manager');
    Route::get('/masters/{id}/edit', [UserController::class, 'edit'])->name('masters.edit')->middleware('role:manager');
    Route::put('/masters/{id}', [UserController::class, 'update'])->name('masters.update')->middleware('role:manager');
    Route::delete('/masters/{id}', [UserController::class, 'destroy'])->name('masters.destroy')->middleware('role:manager');

    Route::resource('/schedules/templates', ScheduleTemplateController::class)->names('schedules.templates');
    Route::resource('/schedules/overrides', ScheduleOverrideController::class)->names('schedules.overrides');

    Route::get('/stock_cards/adjust', [StockCardController::class, 'adjustForm'])->name('stock_cards.adjust');
    Route::post('/stock_cards/adjust', [StockCardController::class, 'storeAdjustment'])->name('stock_cards.adjust.store');
    Route::resource('/stock_cards', StockCardController::class);

    Route::get('/purchases/create-from-order/{purchaseOrder}', [PurchaseController::class, 'createFromOrder'])->name('purchases.createFromOrder')->middleware('role:manager');
    Route::post('/purchases/store-from-order/{purchaseOrder}', [PurchaseController::class, 'storeFromOrder'])->name('purchases.storeFromOrder')->middleware('role:manager');
    Route::get('/purchases', [PurchaseController::class, 'create'])->name('purchases.create')->middleware('role:manager');

    Route::resource('/purchase_requests', PurchaseRequestController::class);
    Route::get('/purchase_requests/{purchaseRequest}/duplicate', [PurchaseRequestController::class, 'duplicate'])->name('purchase_requests.duplicate');
    Route::post('/purchase_requests/{purchaseRequest}/approve', [PurchaseRequestController::class, 'approve'])->name('purchase_requests.approve')->middleware('role:manager');
    Route::post('/purchase_requests/{purchaseRequest}/reject', [PurchaseRequestController::class, 'reject'])->name('purchase_requests.reject')->middleware('role:manager');

    Route::get('/purchase_payments/create/{purchaseInvoiceId}', [PurchasePaymentController::class, 'create'])->name('purchase_payments.create')->middleware('role:manager');
    Route::post('/purchase_payments/store', [PurchasePaymentController::class, 'store'])->name('purchase_payments.store')->middleware('role:manager');

    Route::post('/transactions/storeWithPayment', [TransactionController::class, 'storeWithPayment'])->name('transactions.storeWithPayment');
    Route::get('/transactions/select-medical-record', [MedicalRecordController::class, 'selectForTransaction'])
        ->name('transactions.selectMedicalRecord');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions/{transactionId}/pay-remaining', [TransactionController::class, 'payRemaining'])->name('transactions.payRemaining');
    Route::get('/transactions/create/{medicalRecordId}', [TransactionController::class, 'create'])
        ->name('transactions.create');
    Route::get('/transactions/create', [TransactionController::class, 'createWithoutMedicalRecord'])
        ->name('transactions.createWithoutMedicalRecord');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::post('/transactions/store-without-medical-record', [TransactionController::class, 'storeWithoutMedicalRecord'])->name('transactions.storeWithoutMedicalRecord');
    Route::get('/transactions/{id}/struk', [TransactionController::class, 'showStruk'])->name('transactions.showStruk');
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');

    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::get('/schedules/get-patients', [ScheduleController::class, 'getPatients'])->name('schedules.get-patients');
    Route::post('/schedules/store-reservation', [ScheduleController::class, 'storeReservation'])->name('schedules.store-reservation');

    Route::post('/reservations', [ScheduleController::class, 'storeReservation'])->name('reservations.store');
    Route::get('/reservations', [MedicalRecordController::class, 'list'])->name('reservations.index');
    Route::get('/reservations/{reservation}/edit', [ScheduleController::class, 'editReservation'])->name('reservations.edit');
    Route::put('/reservations/{reservation}', [ScheduleController::class, 'updateReservation'])->name('reservations.update');
    Route::delete('/reservations/{reservation}', [MedicalRecordController::class, 'destroyReservation'])->name('reservations.destroy');
    Route::get('/reservations/get-available-times', [ScheduleController::class, 'getAvailableTimes'])->name('reservations.getAvailableTimes');
    Route::get('/reservations/whatsapp/{id}', [MedicalRecordController::class, 'sendWhatsApp'])->name('reservations.whatsapp');
    Route::get('/reservations/whatsappConfirm/{id}', [MedicalRecordController::class, 'waConfirmation'])->name('reservations.whatsappConfirm');

    Route::prefix('patients/{patientId}')->group(function () {
        Route::get('/medical_records', [MedicalRecordController::class, 'index'])->name('medical_records.index');
        Route::get('/medical_records/create', [MedicalRecordController::class, 'create'])->name('medical_records.create')->middleware('role:dokter_tetap,dokter_luar');
        Route::post('/medical_records', [MedicalRecordController::class, 'store'])->name('medical_records.store')->middleware('role:dokter_tetap,dokter_luar');
        Route::get('/medical_records/{recordId}/edit', [MedicalRecordController::class, 'edit'])->name('medical_records.edit')->middleware('role:dokter_tetap,dokter_luar');
        Route::put('/medical_records/{recordId}', [MedicalRecordController::class, 'update'])->name('medical_records.update')->middleware('role:dokter_tetap,dokter_luar');
    });

    Route::get('/medical_records/{medicalRecordId}/selectMaterials', [MedicalRecordController::class, 'selectMaterials'])->name('medical_records.selectMaterials');
    Route::post('/medical_records/{medicalRecordId}/saveMaterials', [MedicalRecordController::class, 'saveMaterials'])->name('medical_records.saveMaterials');

    Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index')->middleware('role:manager');
    Route::get('attendances/create', [AttendanceController::class, 'create'])->name('attendances.create')->middleware('role:manager');
    Route::post('attendances/store', [AttendanceController::class, 'store'])->name('attendances.store')->middleware('role:manager');
    Route::get('attendances/{attendance}', [AttendanceController::class, 'show'])->name('attendances.show')->middleware('role:manager');
    Route::get('attendances/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendances.edit')->middleware('role:manager');
    Route::put('attendances/{attendance}', [AttendanceController::class, 'update'])->name('attendances.update')->middleware('role:manager');
    Route::delete('attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy')->middleware('role:manager');

    Route::resource('/dental-materials', DentalMaterialController::class);
    Route::resource('procedure_materials', ProcedureMaterialController::class);

    Route::get('/journals', [JournalController::class, 'index'])->name('journals.index')->middleware('role:manager');
    Route::get('/journals/show/{id}', [JournalController::class, 'show'])->name('journals.show')->middleware('role:manager');

    Route::resource('/coa', ChartOfAccountController::class)->middleware('role:manager');
    Route::resource('/holidays', HolidayController::class)->middleware('role:manager');

    Route::resource('expenses', ExpenseController::class);
    Route::get('/expenses/{expense}/duplicate', [ExpenseController::class, 'duplicate'])->name('expenses.duplicate');
    Route::get('/{expense}', [ExpenseController::class, 'show'])->name('dashboard.expenses.show');

    Route::prefix('odontograms')->name('odontograms.')->group(function () {
        Route::get('/{patientId}', [OdontogramController::class, 'index'])->name('index')->middleware('role:dokter_tetap,dokter_luar');
        Route::post('/{patientId}', [OdontogramController::class, 'store'])->name('store')->middleware('role:dokter_tetap,dokter_luar');
    });

    Route::resource('salary_calculations', SalaryCalculationController::class)->middleware('role:manager');

    Route::resource('suppliers', SupplierController::class);

    Route::post('/purchases/{purchase}/pay', [ExpenseController::class, 'payDebt'])->name('purchases.pay')->middleware('role:manager');

    Route::resource('purchases', PurchaseController::class)->middleware('role:manager');

    Route::get('/purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
    Route::post('/purchases/{purchase}/receive', [PurchaseController::class, 'storeReceived'])->name('purchases.storeReceived');

    Route::get('/purchases/{purchase}/pay', [PurchaseController::class, 'showPayForm'])->name('purchases.pay')->middleware('role:manager');
    Route::post('/purchases/pay', [PurchaseController::class, 'payDebt'])->name('purchases.payDebt')->middleware('role:manager');

    Route::get('/reports/balance_sheet', [FinancialReportController::class, 'balanceSheet'])->name('reports.balance_sheet')->middleware('role:manager');
    Route::get('/reports/income_statement', [FinancialReportController::class, 'incomeStatement'])->name('reports.income_statement')->middleware('role:manager');
    Route::get('/reports/cash_flow', [FinancialReportController::class, 'cashFlow'])->name('reports.cash_flow')->middleware('role:manager');
});
