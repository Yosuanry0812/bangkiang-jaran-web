<?php

namespace App\Http\Controllers\Pengelola;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Tiket;
use Illuminate\Http\Request;

class TiketController extends Controller
{
    public function index()
    {
        $tiket = Tiket::withCount(['pemesanan as total_terjual' => function ($q) {
            $q->whereIn('status', ['diproses', 'selesai']);
        }])->orderBy('created_at', 'desc')->paginate(20);

        return view('pengelola.tiket.index', compact('tiket'));
    }

    public function create()
    {
        return view('pengelola.tiket.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tiket' => ['required', 'string', 'max:100'],
            'harga'      => ['required', 'numeric', 'min:0'],
        ], [
            'nama_tiket.required' => 'Nama tiket wajib diisi.',
            'harga.required'      => 'Harga tiket wajib diisi.',
            'harga.numeric'       => 'Harga harus angka.',
        ]);

        Tiket::create($request->only(['nama_tiket', 'harga', 'status']));

        return redirect()->route('pengelola.tiket.index')->with('success', 'Tiket berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $tiket = Tiket::findOrFail($id);
        return view('pengelola.tiket.form', compact('tiket'));
    }

    public function update(Request $request, $id)
    {
        $tiket = Tiket::findOrFail($id);

        $request->validate([
            'nama_tiket' => ['required', 'string', 'max:100'],
            'harga'      => ['required', 'numeric', 'min:0'],
        ]);

        $tiket->update($request->only(['nama_tiket', 'harga', 'status']));

        return redirect()->route('pengelola.tiket.index')->with('success', 'Tiket berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $tiket = Tiket::findOrFail($id);
        ActivityLogger::log('Hapus tiket', 'Nama: ' . $tiket->nama_tiket);
        $tiket->delete(); // soft delete

        return redirect()->route('pengelola.tiket.index')->with('success', 'Tiket berhasil dihapus.');
    }
}
