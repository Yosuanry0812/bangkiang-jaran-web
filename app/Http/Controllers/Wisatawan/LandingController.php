<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Models\Konten;
use App\Models\Galeri;
use App\Models\Tiket;

class LandingController extends Controller
{
    public function index()
    {
        $kontenList = Konten::orderBy('created_at', 'desc')->get();
        $galeri = Galeri::latest()->get();
        $tiket = Tiket::aktif()->get();

        return view('wisatawan.landing', compact('kontenList', 'galeri', 'tiket'));
    }

}
