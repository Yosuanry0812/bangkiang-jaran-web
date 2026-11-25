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
        $tiket = Tiket::aktif()->get();
        $sejarahFoto = Galeri::sejarah()->orderBy('id_galeri')->get();

        return view('wisatawan.landing', compact('galeri', 'tiket', 'sejarahFoto'));
    }

    public function restoran()
    {
        $galeriRestoran = Galeri::restoran()->latest()->get();

        return view('wisatawan.restoran', compact('galeriRestoran'));
    }

}
