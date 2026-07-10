<?php

namespace App\Http\Controllers\Pengelola;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Konten;
use Illuminate\Http\Request;

class KontenController extends Controller
{
    public function index()
    {
        $konten = Konten::orderBy('created_at', 'desc')->paginate(20);
        return view('pengelola.konten.index', compact('konten'));
    }

    public function create()
    {
        return view('pengelola.konten.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'isi'   => ['required', 'string'],
            'jenis' => ['required', 'string', 'max:50'],
        ], [
            'judul.required' => 'Judul wajib diisi.',
            'isi.required'   => 'Konten wajib diisi.',
            'jenis.required' => 'Jenis konten wajib diisi.',
        ]);

        Konten::create($request->only(['judul', 'isi', 'jenis']));

        return redirect()->route('pengelola.konten.index')->with('success', 'Konten berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $konten = Konten::findOrFail($id);
        return view('pengelola.konten.form', compact('konten'));
    }

    public function update(Request $request, $id)
    {
        $konten = Konten::findOrFail($id);

        $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'isi'   => ['required', 'string'],
            'jenis' => ['required', 'string', 'max:50'],
        ]);

        $konten->update($request->only(['judul', 'isi', 'jenis']));

        return redirect()->route('pengelola.konten.index')->with('success', 'Konten berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $konten = Konten::findOrFail($id);
        ActivityLogger::log('Hapus konten', 'Judul: ' . $konten->judul);
        $konten->delete(); // soft delete

        return redirect()->route('pengelola.konten.index')->with('success', 'Konten berhasil dihapus.');
    }
}
