<?php

namespace App\Http\Controllers\Pengelola;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Galeri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GaleriController extends Controller
{
    public function index(Request $request)
    {
        $query = Galeri::orderBy('created_at', 'desc');

        if ($request->tipe && in_array($request->tipe, ['wisata', 'restoran'])) {
            $query->where('tipe', $request->tipe);
        }

        $galeri = $query->paginate(20);
        return view('pengelola.galeri.index', compact('galeri'));
    }

    public function create()
    {
        return view('pengelola.galeri.form');
    }

    public function store(Request $request)
    {
        $rules = [
            'file'           => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'keterangan'     => ['nullable', 'string', 'max:255'],
            'keterangan_en'  => ['nullable', 'string', 'max:255'],
            'tipe'           => ['required', 'in:wisata,restoran'],
        ];

        $messages = [
            'file.required' => 'File wajib diupload.',
            'file.image'    => 'File harus berupa gambar.',
            'file.mimes'    => 'Format foto: jpg, jpeg, png.',
            'file.max'      => 'Ukuran maksimal 5MB.',
            'tipe.required' => 'Pilih tipe galeri.',
        ];

        $request->validate($rules, $messages);

        // Validasi MIME type server-side
        $file = $request->file('file');
        $allowedMimes = ['image/jpeg', 'image/png'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return back()->withErrors(['file' => 'Tipe file foto tidak valid.'])->withInput();
        }

        $filename = 'galeri_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/galeri', $filename);
        $pathDisplay = str_replace('public/', '', $path);

        Galeri::create([
            'file'           => $pathDisplay,
            'keterangan'     => $request->keterangan,
            'keterangan_en'  => $request->keterangan_en,
            'tipe'           => $request->tipe,
        ]);

        return redirect()->route('pengelola.galeri.index')->with('success', 'Foto berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $galeri = Galeri::findOrFail($id);
        if ($galeri->file) {
            Storage::delete('public/' . $galeri->file);
        }
        ActivityLogger::log('Hapus galeri', 'File: ' . $galeri->file);
        $galeri->delete();

        return redirect()->route('pengelola.galeri.index')->with('success', 'Data berhasil dihapus.');
    }
}
