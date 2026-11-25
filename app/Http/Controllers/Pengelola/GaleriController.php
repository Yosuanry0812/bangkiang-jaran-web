<?php

namespace App\Http\Controllers\Pengelola;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MediaVariantController;
use App\Models\Galeri;
use App\Services\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GaleriController extends Controller
{
    public function index(Request $request)
    {
        $query = Galeri::orderBy('created_at', 'desc');

        if ($request->tipe && in_array($request->tipe, ['wisata', 'restoran', 'sejarah'])) {
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
            'file' => ['required', 'array', 'max:20'],
            'file.*' => ['image', 'mimes:jpg,jpeg,png', 'max:51200'],
            'tipe' => ['required', 'in:wisata,restoran,sejarah'],
        ];

        $messages = [
            'file.required' => __('flash.gallery_file_required'),
            'file.max' => __('flash.gallery_files_count'),
            'file.*.image' => __('flash.gallery_file_image'),
            'file.*.mimes' => __('flash.gallery_file_mimes'),
            'file.*.max' => __('flash.gallery_file_max'),
            'file.*.uploaded' => __('flash.gallery_file_max'),
            'tipe.required' => __('flash.gallery_type_required'),
        ];

        $request->validate($rules, $messages);

        // Validasi MIME type server-side
        $files = $request->file('file');
        foreach ($files as $file) {
            if (! in_array($file->getMimeType(), ['image/jpeg', 'image/png'])) {
                return back()->withErrors(['file' => __('flash.gallery_file_invalid')])->withInput();
            }
        }

        @set_time_limit(600);

        foreach ($files as $i => $file) {
            $filename = 'galeri_'.uniqid().'.'.strtolower($file->getClientOriginalExtension());
            $path = $file->storeAs('public/galeri', $filename);
            $pathDisplay = str_replace('public/', '', $path);

            Galeri::create([
                'file' => $pathDisplay,
                'tipe' => $request->tipe,
            ]);

            MediaVariantController::warmInBackground($pathDisplay);
            AuditTrail::record('Tambah galeri', $pathDisplay);
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'count' => count($files)]);
        }

        return redirect()->route('pengelola.galeri.index')->with('success', count($files) > 1
            ? __('flash.gallery_added_many', ['count' => count($files)])
            : __('flash.gallery_added'));
    }

    public function destroy($id)
    {
        $galeri = Galeri::findOrFail($id);
        if ($galeri->file) {
            Storage::delete('public/'.$galeri->file);
        }
        ActivityLogger::log('Hapus galeri', 'File: '.$galeri->file);
        $galeri->delete();

        return redirect()->route('pengelola.galeri.index')->with('success', __('flash.data_deleted'));
    }
}
