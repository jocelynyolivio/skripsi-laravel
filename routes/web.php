<?php

use App\Models\Category;
use App\Models\HomeContent;
use Illuminate\Http\Request;
use App\Models\SalaryCalculation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\OdontogramController;
use App\Http\Controllers\HomeContentController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\PatientLoginController;
use App\Http\Controllers\DashboardPostController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\DentalMaterialController;
use App\Http\Controllers\ExpenseRequestController;
use App\Http\Controllers\ScheduleOverrideController;
use App\Http\Controllers\ScheduleTemplateController;
use App\Http\Controllers\ProcedureMaterialController;
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

// Route::get('/', function () {
//     // Route::get('/about', function () {
//     // ini artinya nde url nti hrus ada about e
//     $contents = HomeContent::all();
//     return view('home', [
//         "title" => "homeee",
//         "active" => 'home',
//         'contents' => $contents,
//     ]);
//     // ini nanti brti folder view, file welcome.blade.php
//     // return 'Hello World';
// });
Route::get('/', function () {
    $contents = HomeContent::all();
    return view('home', [
        "title" => "SenyumQu",
        "active" => "home",
        "contents" => $contents,
    ]);
});
Route::get('/dashboard/home_content/{homeContent}/edit', [HomeContentController::class, 'edit'])->name('dashboard.home_content.edit');


// Route::get('/', [HomeContentController::class, 'index'])->name('home');

Route::get('/about', function () {
    // return 'Halaman About';
    return view('about', [
        "title" => "abouttt",
        "name" => "Jocelyn Y",
        "email" => "jocelynyolivio.jy@gmail.com",
        "image" => "yoli.jpg",
        "active" => 'about'
    ]);
});

// pake controller
Route::get('/blog', [PostController::class, 'index']);

Route::get('/post/{post:slug}', [PostController::class, 'show']);

Route::get('/categories', function () {
    return view('categories', [
        'title' => 'Post Categories',
        'category' => Category::all(),
        "active" => 'categories'
    ]);
});

Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate']);

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/patient/register', [RegisterController::class, 'index'])->name('register')->middleware('guest');
// kalo ada req ke halaman register tapi method post maka nanti akan panggil yg store
Route::post('/patient/register', [RegisterController::class, 'store']);

Route::resource('/dashboard/posts', DashboardPostController::class)->middleware('auth');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('internal')
    ->name('dashboard');

Route::get('/reservation', [ReservationController::class, 'index'])
    ->name('reservation.index')
    ->middleware(['auth:patient', 'verified']);

Route::post('/reservation', [ReservationController::class, 'store'])->name('reservation.store')->middleware('patient');


// Route::middleware(['auth:patient', 'verified'])->group(function () {
//     Route::get('/reservation', [ReservationController::class, 'index'])->name('reservation.index');
//     Route::post('/reservation', [ReservationController::class, 'store'])->name('reservation.store');
// });
// isi dashboarrrddddd
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/schedules/get-patients', [ScheduleController::class, 'getPatients'])->name('schedules.get-patients');
    Route::post('/schedules/store-reservation', [ScheduleController::class, 'storeReservation'])->name('schedules.store-reservation');

//     Route::get('/reservation/edit-reservation/{id}', [ScheduleController::class, 'editReservation'])->name('schedules.edit-reservation');
// Route::post('/schedules/update-reservation/{id}', [ScheduleController::class, 'updateReservation'])->name('schedules.update-reservation');
// Route::get('/schedules/get-available-times', [ScheduleController::class, 'getAvailableTimes']);


    Route::post('/reservations', [ScheduleController::class, 'storeReservation'])->name('reservations.store');

    Route::get('/reservations', [ReservationController::class, 'list'])->name('reservations.index');


    Route::resource('/home_content', HomeContentController::class);

    // Route untuk profil pengguna
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/{user}', [ProfileController::class, 'update'])->name('profile.update');

    // Route untuk jadwal (Schedules)
    // Route::resource('schedules', SchedulesController::class);
    // Route::get('/schedules', [SchedulesController::class, 'index'])->name('schedules.index');
    // Route::get('/schedules/create', [SchedulesController::class, 'create'])->name('schedules.create');
    // Route::post('/schedules', [SchedulesController::class, 'store'])->name('schedules.store');
    // Route::get('/schedules/{schedule}/edit', [SchedulesController::class, 'edit'])->name('schedules.edit');
    // Route::put('/schedules/{schedule}', [SchedulesController::class, 'update'])->name('schedules.update');
    // Route::delete('/schedules/{schedule}', [SchedulesController::class, 'destroy'])->name('schedules.destroy');

    // Route untuk reservasi
    // Route::get('/reservations', [ReservationController::class, 'list'])->name('reservations.index');
    Route::get('/reservations/{reservation}/edit', [ScheduleController::class, 'editReservation'])->name('reservations.edit');
    Route::put('/reservations/{reservation}', [ScheduleController::class, 'updateReservation'])->name('reservations.update');
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
    Route::get('/reservations/get-available-times', [ScheduleController::class, 'getAvailableTimes'])->name('reservations.getAvailableTimes');
    // // Admin: Menampilkan form tambah reservasi
    // Route::get('/reservations/create', [ReservationController::class, 'createForAdmin'])
    //     ->name('reservations.create');

    // // Admin: Menyimpan data reservasi
    // Route::post('/reservations', [ReservationController::class, 'storeForAdmin'])
    //     ->name('reservations.store');

    Route::get('/masters/patients/{id}/edit', [PatientController::class, 'edit'])->name('masters.patients.edit');
    Route::put('/masters/patients/{id}', [PatientController::class, 'update'])->name('masters.patients.update');
    Route::delete('/masters/patients/{id}', [PatientController::class, 'destroy'])->name('masters.patients.destroy');
    Route::get('/masters/patients', [PatientController::class, 'index'])->name('masters.patients');
    Route::get('/masters/patients/create', [PatientController::class, 'create'])->name('masters.patients.create');
    Route::post('/masters/patients', [PatientController::class, 'store'])->name('masters.patients.store');

    // Route::get('/masters/{role_id}/role', [UserController::class, 'showByRole'])->name('masters.role');
    // Route::get('/masters/{role_id}/role/{id}/edit', [UserController::class, 'edit'])->name('masters.users.edit');
    // Route::put('/masters/{role_id}/role/{id}', [UserController::class, 'update'])->name('masters.users.update');
    // Route::delete('/masters/{role_id}/role/{id}', [UserController::class, 'destroy'])->name('masters.users.destroy');
    Route::get('/masters', [UserController::class, 'index'])->name('masters.index');
Route::get('/masters/{id}/edit', [UserController::class, 'edit'])->name('masters.edit');
Route::put('/masters/{id}', [UserController::class, 'update'])->name('masters.update');
Route::delete('/masters/{id}', [UserController::class, 'destroy'])->name('masters.destroy');


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

    Route::get('/reservations/whatsapp/{id}', [ReservationController::class, 'sendWhatsApp'])->name('reservations.whatsapp');

    Route::resource('/dental-materials', DentalMaterialController::class);
    Route::get('/medical_records/{medicalRecordId}/selectMaterials', [MedicalRecordController::class, 'selectMaterials'])
    ->name('medical_records.selectMaterials');

    Route::post('/medical_records/{medicalRecordId}/saveMaterials', [MedicalRecordController::class, 'saveMaterials'])
    ->name('medical_records.saveMaterials');

    Route::get('/transactions/select-medical-record', [MedicalRecordController::class, 'selectForTransaction'])
    ->name('transactions.selectMedicalRecord');

    Route::resource('procedure_materials', ProcedureMaterialController::class);

    // Halaman index transaksi (daftar semua transaksi)
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

    // Membuat transaksi baru dari rekam medis
    Route::get('/transactions/create/{medicalRecordId}', [TransactionController::class, 'create'])->name('transactions.create');

    // Menyimpan transaksi baru
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');

    Route::get('/transactions/{id}/struk', [TransactionController::class, 'showStruk'])->name('transactions.showStruk');

    Route::resource('expenses', ExpenseController::class);

    Route::get('dental-materials/report', [DentalMaterialController::class, 'report'])->name('dental-materials.report');

    Route::resource('expense_requests', ExpenseRequestController::class);
    Route::patch('expense_requests/{id}/approve', [ExpenseRequestController::class, 'approve'])->name('expense_requests.approve');
    Route::patch('expense_requests/{id}/reject', [ExpenseRequestController::class, 'reject'])->name('expense_requests.reject');
    Route::patch('expense_requests/{id}/done', [ExpenseRequestController::class, 'markDone'])->name('expense_requests.done');

    Route::get('/salaries/upload-salary', [SalaryController::class, 'uploadForm']);
Route::post('/salaries/process-salary', [SalaryController::class, 'processExcel']);


Route::get('/salaries', [SalaryController::class, 'index'])->name('salaries.index');

Route::get('/salaries/slip', [SalaryController::class, 'userSalarySlip'])->name('salaries.slip');

Route::post('/salaries/process', [SalaryController::class, 'processSalaries'])->name('salaries.process');

Route::post('/salaries/calculate', [SalaryController::class, 'calculateSalaries'])->name('salaries.calculate');
Route::post('/salaries/doctors', [SalaryController::class, 'calculateDoctorSalaries'])->name('salaries.doctor');

// Route::resource('attendances', AttendanceController::class);

Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('attendances/create', [AttendanceController::class, 'create'])->name('attendances.create');
    Route::post('attendances/store', [AttendanceController::class, 'store'])->name('attendances.store');
    Route::get('attendances/{attendance}', [AttendanceController::class, 'show'])->name('attendances.show');
    Route::get('attendances/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendances.edit');
    Route::put('attendances/{attendance}', [AttendanceController::class, 'update'])->name('attendances.update');
    Route::delete('attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');


Route::post('/salaries/store', [SalaryController::class, 'storeSalaries'])->name('salaries.store');

Route::resource('holidays', HolidayController::class);









Route::get('/salaries', [SalaryController::class, 'index'])->name('salaries.index');
Route::get('/salaries/data', [SalaryController::class, 'getSalaryData'])->name('salaries.data');



// Rute untuk odontogram
Route::prefix('odontograms')->name('odontograms.')->group(function () {
    // Menampilkan halaman odontogram pasien
    Route::get('/{patientId}', [OdontogramController::class, 'index'])->name('index');
    
    // Menyimpan atau memperbarui data odontogram
    Route::post('/{patientId}', [OdontogramController::class, 'store'])->name('store');
});
});

    // Route::get('/dashboard/salaries/', function () {
    //     $user = Auth::user(); // Mendapatkan pengguna yang sedang login
    //     // $user->load('role'); // Memuat relasi role
    //     return view('dashboard.salaries.index');
    // })->middleware('internal');


// Route::get('/odontogram', function () {
//     return view('dashboard.odontogram.show', [
//         "title" => "odontogram"
//     ]);
// });

// Route::get('/dashboard/odontogram/{medicalRecordId}', [OdontogramController::class, 'show'])->name('dashboard.odontogram.show');
// Route::get('/dashboard/odontogram/{medicalRecordId}', [OdontogramController::class, 'store'])->name('dashboard.odontogram.store');
// Route::get('/dashboard/odontogram', function () {
//     return view('dashboard.odontogram.index');
// })->name('dashboard.odontogram.index');


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

// Routes untuk user profile (non-dashboard)
Route::middleware(['auth:patient'])->group(function () {
    Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [UserProfileController::class, 'update'])->name('profile.update');
});

Route::get('/dashboard/schedules', [ScheduleController::class, 'index'])->name('dashboard.schedules.index');

// Rute untuk mendapatkan dokter berdasarkan tanggal
Route::get('/dashboard/schedules/get-doctors-by-date', [ScheduleController::class, 'getDoctorsByDate'])
    ->name('dashboard.schedules.get-doctors-by-date');

// Rute untuk template dan override jadwal
Route::prefix('dashboard/schedules')->name('dashboard.schedules.')->group(function () {
    Route::resource('templates', ScheduleTemplateController::class);
    Route::resource('overrides', ScheduleOverrideController::class)->except(['show']);
});
