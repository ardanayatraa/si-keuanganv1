<?php

namespace App\Http\Controllers;

use App\Models\Anggaran;
use App\Models\KategoriPengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnggaranController extends Controller
{
    /**
     * Tampilkan daftar anggaran milik user login.
     */
    public function index()
    {
        $items = Anggaran::with('kategori')
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->orderBy('periode_awal', 'desc') // optional: urut per periode
            ->get();

        return view('anggaran.index', compact('items'));
    }

    /**
     * Form tambah anggaran.
     */
    public function create()
    {
        $listKategori = KategoriPengeluaran::where('id_pengguna', Auth::user()->id_pengguna)
            ->get();
        return view('anggaran.create', compact('listKategori'));
    }

    /**
     * Simpan data anggaran baru untuk user login.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_kategori'    => 'required|exists:kategori_pengeluaran,id_kategori_pengeluaran',
            'deskripsi'      => 'nullable|string',
            'jumlah_batas'   => 'required|numeric|min:0',
            'periode_awal'   => 'required|date',
            'periode_akhir'  => 'required|date|after_or_equal:periode_awal',
        ]);

        // assign id_pengguna otomatis
        $data['id_pengguna'] = Auth::user()->id_pengguna;

        Anggaran::create($data);

        return redirect()
            ->route('anggaran.index')
            ->with('success', 'Anggaran berhasil dibuat.');
    }

    /**
     * Tampilkan detail anggaran (pastikan milik user).
     */
    public function show($id)
    {
        $anggaran = Anggaran::with('kategori')
            ->where('id_anggaran', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        return view('anggaran.show', compact('anggaran'));
    }

    /**
     * Form edit anggaran (pastikan milik user).
     */
    public function edit($id)
    {
        $anggaran = Anggaran::where('id_anggaran', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        $listKategori = KategoriPengeluaran::where('id_pengguna', Auth::user()->id_pengguna)
            ->get();
        return view('anggaran.edit', compact('anggaran', 'listKategori'));
    }

    /**
     * Update anggaran (pastikan milik user).
     */
    public function update(Request $request, $id)
    {
        $anggaran = Anggaran::where('id_anggaran', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        $data = $request->validate([
            'id_kategori'    => 'required|exists:kategori_pengeluaran,id_kategori_pengeluaran',
            'deskripsi'      => 'nullable|string',
            'jumlah_batas'   => 'required|numeric|min:0',
            'periode_awal'   => 'required|date',
            'periode_akhir'  => 'required|date|after_or_equal:periode_awal',
        ]);

        $anggaran->update($data);

        return redirect()
            ->route('anggaran.index')
            ->with('success', 'Anggaran berhasil diperbarui.');
    }

    /**
     * Hapus anggaran (pastikan milik user).
     */
    public function destroy($id)
    {
        $anggaran = Anggaran::where('id_anggaran', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        $anggaran->delete();

        return redirect()
            ->route('anggaran.index')
            ->with('success', 'Anggaran berhasil dihapus.');
    }
}
