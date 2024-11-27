<?php

// use App\Models\Post;
// use App\Models\User;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\PatientLoginController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\DashboardPostController;
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
// Route::get('/about', function () {
// ini artinya nde url nti hrus ada about e

    return view('home',[
        "title" => "homeee",
        "active" => 'home'
    ]);
    // ini nanti brti folder view, file welcome.blade.php
    // return 'Hello World';
});

Route::get('/about', function () {
    // return 'Halaman About';
    return view('about',[
        "title" => "abouttt",
        "name" => "Jocelyn Y",
        "email" => "jocelynyolivio.jy@gmail.com",
        "image" => "yoli.jpg",
        "active" => 'about'
    ]);
});


// Route::get('/blog', function () {
//     return view('posts', [
//         "title" => "blog",
//         "posts" => Post::all()
//     ]);
// });

// halaman single post
// Route::get('/post/{slug}', function($slug){
//     return view('post', [
//         "title" => "Single Post",
//         "post" => Post::find($slug)
//     ]);
// });

// pake controller
Route::get('/blog', [PostController::class, 'index']);

// Route::get('/post/{slug}', [PostController::class, 'show']);

Route::get('/post/{post:slug}', [PostController::class, 'show']);

// Route::get('/categories/{category:slug}', function(Category $category){
//     return view('category',[
//         'title' => "Post By Category : $category->name",
//         'posts' => $category->posts,
//         // 'category' => $category->name
//         "active" => 'post'
//     ]);
// });

Route::get('/categories', function(){
    return view('categories',[
        'title' => 'Post Categories',
        'category' => Category::all(),
        "active" => 'categories'
    ]);
});

// Route::get('/authors/{user}', function(User $user){
//     return view('posts',[
//         'title' => "Post By Authors : $user->name",
//         'posts' => $user->posts,
//     ]);
// });

Route::get('/login', [LoginController::class, 'index'] )->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate'] );

Route::post('/logout', [LoginController::class, 'logout'] )->name('logout')->middleware('auth');

Route::get('/patient/register', [RegisterController::class, 'index'] )->name('register')->middleware('guest');
// kalo ada req ke halaman register tapi method post maka nanti akan panggil yg store
Route::post('/patient/register', [RegisterController::class, 'store'] );

Route::resource('/dashboard/posts', DashboardPostController::class)->middleware('auth');

// Route::get('/dashboard/posts/checkSlug', [DashboardPostController::class, 'checkSlug'])->middleware('auth');

// Route::resource('/dashboard/categories', AdminCategoryController::class)->except('show')->middleware('admin');

// Route::get('/dashboard', function () {
//     $user = Auth::user(); // Mendapatkan pengguna yang sedang login
//     return view('dashboard.index', [
//         'user' => $user,
//         'role' => $user->role ? $user->role->role_name : 'No Role',
//     ]);
// })->middleware('internal');
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('internal')
    ->name('dashboard');

Route::get('/reservation', [ReservationController::class, 'index'])
    ->name('reservation.index')
    ->middleware(['auth:patient', 'verified']);

    Route::post('/reservation', [ReservationController::class, 'store'])->name('reservation.store')->middleware('patient');

    // Route::get('/dashboard/masters', [UserController::class, 'index'])->name('dashboard.masters.index');

    Route::get('/dashboard/masters/{role_id}', [UserController::class, 'showByRole'])->name('dashboard.masters.role')->middleware('internal');

    Route::get('/dashboard/reservations', [ReservationController::class, 'list'])
    ->name('dashboard.reservations.index')
    ->middleware('auth');

    Route::middleware(['auth'])->group(function () {
        // Menampilkan profil pengguna
        Route::get('/dashboard/profile', [ProfileController::class, 'show'])->name('profile.show');
        
        // Menampilkan form untuk mengedit profil
        Route::get('/dashboard/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        
        // Mengupdate profil
        Route::put('/dashboard/profile/{user}', [ProfileController::class, 'update'])->name('profile.update');
    });

    Route::get('/dashboard/salaries/', function () {
        $user = Auth::user(); // Mendapatkan pengguna yang sedang login
        // $user->load('role'); // Memuat relasi role
        return view('dashboard.salaries.index');
    })->middleware('internal');
    

    Route::get('/odontogram', function () {
            return view('odontogram',[
                "title" => "odontogram"
            ]);
        });


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
        