<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //menampilkan dashboard
    public function index()
    {
        //menampilkan view dashboardnya saja
        return view('dashboard');
    }
}
