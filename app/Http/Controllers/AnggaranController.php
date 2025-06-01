<?php

namespace App\Http\Controllers;

use App\Models\Anggaran;
use App\Models\Pengguna;
use App\Models\KategoriPengeluaran;
use Illuminate\Http\Request;

class AnggaranController extends Controller
{
    /**
     * Tampilkan daftar semua anggaran beserta relasi kategori & pengguna.
     */
    public function index()
    {
        $items = Anggaran::with('kategori', 'pengguna')->get();
        return view('anggaran.index', compact('items'));
    }

    /**
     * Tampilkan form untuk membuat anggaran baru.
     * Perlu menyediakan data pengguna dan kategori untuk dropdown.
     */
    public function create()
    {
        $listPengguna = Pengguna::all();                  // Untuk dropdown pengguna
        $listKategori = KategoriPengeluaran::all();       // Untuk dropdown kategori
        return view('anggaran.create', compact('listPengguna', 'listKategori'));
    }

    /**
     * Simpan anggaran baru ke database.
     * Validasi input: periode_awal dan periode_akhir sebagai tanggal.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_pengguna'    => 'required|exists:pengguna,id_pengguna',
            'id_kategori'    => 'required|exists:kategori_pengeluaran,id_kategori_pengeluaran',
            'deskripsi'      => 'nullable|string',
            'jumlah_batas'   => 'required|numeric|min:0',
            'periode_awal'   => 'required|date',
            'periode_akhir'  => 'required|date|after_or_equal:periode_awal',
        ]);

        Anggaran::create([
            'id_pengguna'   => $data['id_pengguna'],
            'id_kategori'   => $data['id_kategori'],
            'deskripsi'     => $data['deskripsi'] ?? null,
            'jumlah_batas'  => $data['jumlah_batas'],
            'periode_awal'  => $data['periode_awal'],
            'periode_akhir' => $data['periode_akhir'],
        ]);

        return redirect()
            ->route('anggaran.index')
            ->with('success', 'Anggaran berhasil dibuat.');
    }

    /**
     * Tampilkan detail anggaran tertentu.
     */
    public function show(Anggaran $anggaran)
    {
        return view('anggaran.show', compact('anggaran'));
    }

    /**
     * Tampilkan form edit untuk anggaran yang dipilih.
     * Sertakan data pengguna & kategori untuk pilihan dropdown.
     */
    public function edit(Anggaran $anggaran)
    {
        $listPengguna = Pengguna::all();
        $listKategori = KategoriPengeluaran::all();
        return view('anggaran.edit', compact('anggaran', 'listPengguna', 'listKategori'));
    }

    /**
     * Update data anggaran yang sudah ada.
     */
    public function update(Request $request, Anggaran $anggaran)
    {
        $data = $request->validate([
            'id_pengguna'    => 'required|exists:pengguna,id_pengguna',
            'id_kategori'    => 'required|exists:kategori_pengeluaran,id_kategori_pengeluaran',
            'deskripsi'      => 'nullable|string',
            'jumlah_batas'   => 'required|numeric|min:0',
            'periode_awal'   => 'required|date',
            'periode_akhir'  => 'required|date|after_or_equal:periode_awal',
        ]);

        $anggaran->update([
            'id_pengguna'   => $data['id_pengguna'],
            'id_kategori'   => $data['id_kategori'],
            'deskripsi'     => $data['deskripsi'] ?? null,
            'jumlah_batas'  => $data['jumlah_batas'],
            'periode_awal'  => $data['periode_awal'],
            'periode_akhir' => $data['periode_akhir'],
        ]);

        return redirect()
            ->route('anggaran.index')
            ->with('success', 'Anggaran berhasil diperbarui.');
    }

    /**
     * Hapus anggaran yang dipilih.
     */
    public function destroy(Anggaran $anggaran)
    {
        $anggaran->delete();
        return redirect()
            ->route('anggaran.index')
            ->with('success', 'Anggaran berhasil dihapus.');
    }
}
