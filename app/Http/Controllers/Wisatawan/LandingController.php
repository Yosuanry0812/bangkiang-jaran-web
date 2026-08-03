<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use App\Models\Tiket;

class LandingController extends Controller
{
    public function index()
    {
        $galeri = Galeri::wisata()->latest()->get();
        $galeriRestoran = Galeri::restoran()->latest()->get();
        $tiket = Tiket::aktif()->get();

        return view('wisatawan.landing', compact('galeri', 'galeriRestoran', 'tiket'));
    }

}
