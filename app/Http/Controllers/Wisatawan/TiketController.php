<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use Illuminate\Http\Request;

class TiketController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $tiketList = Tiket::aktif()->get();

        return view('wisatawan.tiket', compact('tiketList', 'tanggal'));
    }

    public function detail($id)
    {
        $tiket = Tiket::aktif()->findOrFail($id);
        return view('wisatawan.tiket-detail', compact('tiket'));
    }
}
