<?php

namespace App\Http\Controllers\Pengelola;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Tiket;
use App\Services\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TiketController extends Controller
{
    public function index()
    {
        $tiket = Tiket::withCount(['details as total_terjual' => function ($q) {
            $q->whereHas('pemesanan', fn ($b) => $b->approved());
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
            'nama_tiket_en' => ['nullable', 'string', 'max:100'],
            'harga' => ['required', 'integer', 'min:0', 'max:99999999'],
            'kategori' => ['nullable', 'in:perorangan'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ], [
            'nama_tiket.required' => __('flash.ticket_name_required'),
            'harga.required' => __('flash.ticket_price_required'),
            'harga.numeric' => 'Harga harus angka.',
        ]);

        $data = $request->only(['nama_tiket', 'nama_tiket_en', 'harga', 'status']);
        $data['kategori'] = 'perorangan';
        DB::transaction(function () use ($data) {
            $ticket = Tiket::create($data);
            AuditTrail::record('Tambah tiket', 'ID: '.$ticket->id_tiket);
        });

        return redirect()->route('pengelola.tiket.index')->with('success', __('flash.ticket_added'));
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
            'nama_tiket_en' => ['nullable', 'string', 'max:100'],
            'harga' => ['required', 'integer', 'min:0', 'max:99999999'],
            'kategori' => ['nullable', 'in:perorangan'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        $data = $request->only(['nama_tiket', 'nama_tiket_en', 'harga', 'status']);
        $data['kategori'] = 'perorangan';
        DB::transaction(function () use ($tiket, $data) {
            $tiket->update($data);
            AuditTrail::record('Ubah tiket', 'ID: '.$tiket->id_tiket.'; '.implode(', ', array_keys($tiket->getChanges())));
        });

        return redirect()->route('pengelola.tiket.index')->with('success', __('flash.ticket_updated'));
    }

    public function destroy($id)
    {
        $tiket = Tiket::findOrFail($id);
        ActivityLogger::log('Hapus tiket', 'Nama: '.$tiket->nama_tiket);
        $tiket->delete(); // soft delete

        return redirect()->route('pengelola.tiket.index')->with('success', __('flash.ticket_deleted'));
    }
}
