<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    public function index()
    {
        $anggotas = Anggota::with('penjualans')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('anggota.index', compact('anggotas'));
    }

    public function create()
    {
        return view('anggota.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_member'    => 'required|unique:anggotas,kode_member|max:20',
            'nama'           => 'required|string|max:100',
            'alamat'         => 'nullable|string|max:150',
            'no_hp'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:100',
            'tanggal_daftar' => 'required|date',
        ]);

        $validated['status_aktif'] = 'aktif';
        $validated['poin'] = 0;
        $validated['total_belanja'] = 0;

        Anggota::create($validated);

        return redirect()->route('anggota.index')->with('success', 'Member pelanggan berhasil didaftarkan!');
    }
}
