<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('dashboard.index', [
            'title' => 'Dashboard',
            'user' => $user,
            'role' => $user->role->role_name
        ]);
    }
}

