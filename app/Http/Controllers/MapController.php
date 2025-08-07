<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        $umkms = Umkm::whereNotNull('latitude')->whereNotNull('longitude')->get();
        return view('peta.index', compact('umkm'));
    }
}
