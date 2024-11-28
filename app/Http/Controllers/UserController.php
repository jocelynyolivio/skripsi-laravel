<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // public function index()
    // {
    //     $users = User::where('role_id', 4)->get(); // Hanya mengambil data dengan role_id = 4
    //     return view('dashboard.masters.index', compact('users'));
    // }

    public function showByRole($role_id)
{
    $users = User::where('role_id', $role_id)->get();
    return view('dashboard.masters.index', compact('users', 'role_id'));
}

}
