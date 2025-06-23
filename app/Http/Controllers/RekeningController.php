<?php

namespace App\Http\Controllers;

use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RekeningController extends Controller
{
    /**
     * Tampilkan daftar rekening milik user login.
     */
    public function index()
    {
        $items = Rekening::where('id_pengguna', Auth::user()->id_pengguna)
                         ->get();

        return view('rekening.index', compact('items'));
    }

    /**
     * Form tambah rekening.
     */
    public function create()
    {
        return view('rekening.create');
    }

    /**
     * Simpan rekening baru untuk user login.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_rekening' => 'required|string|max:50',
            'saldo'         => 'required|numeric',
        ]);

        $data['id_pengguna'] = Auth::user()->id_pengguna;
        Rekening::create($data);

        return redirect()->route('rekening.index')
                         ->with('success', 'Rekening berhasil dibuat.');
    }

    /**
     * Tampilkan detail rekening (pastikan milik user).
     */
    public function show($id)
    {
        $rekening = Rekening::where('id_rekening', $id)
                            ->where('id_pengguna', Auth::user()->id_pengguna)
                            ->firstOrFail();

        return view('rekening.show', compact('rekening'));
    }

    /**
     * Form edit rekening (pastikan milik user).
     */
    public function edit($id)
    {
        $rekening = Rekening::where('id_rekening', $id)
                            ->where('id_pengguna', Auth::user()->id_pengguna)
                            ->firstOrFail();

        return view('rekening.edit', compact('rekening'));
    }

    /**
     * Update rekening (pastikan milik user).
     */
    public function update(Request $request, $id)
    {
        $rekening = Rekening::where('id_rekening', $id)
                            ->where('id_pengguna', Auth::user()->id_pengguna)
                            ->firstOrFail();

        $data = $request->validate([
            'nama_rekening' => 'required|string|max:50',
            'saldo'         => 'required|numeric',
        ]);

        $rekening->update($data);

        return redirect()->route('rekening.index')
                         ->with('success', 'Rekening berhasil diperbarui.');
    }

    /**
     * Hapus rekening & semua data yang berelasi (tanpa ubah migrasi).
     */
    public function destroy($id)
    {
        $rekening = Rekening::where('id_rekening', $id)
                            ->where('id_pengguna', Auth::user()->id_pengguna)
                            ->firstOrFail();

            DB::transaction(function() use ($rekening) {
            // 1) Hapus semua pembayaran utang
            $rekening->pembayaranUtangs()->delete();

            // 2) Hapus semua pembayaran piutang
            $rekening->pembayaranPiutangs()->delete();

            // 3) Hapus semua transfer (keluar & masuk)
            $rekening->transfers()->delete();
            $rekening->transfersMasuk()->delete();

            // 4) Hapus semua utang yang pakai rekening ini
            $rekening->utangs()->delete();

            // 5) Hapus semua piutang yang pakai rekening ini
            $rekening->piutangs()->delete();

            // 6) Hapus semua pengeluaran & pemasukan
            $rekening->pengeluarans()->delete();
            $rekening->pemasukans()->delete();

            // 7) Terakhir, hapus rekening
            $rekening->delete();
        });

        return redirect()->route('rekening.index')
                         ->with('success', 'Rekening dan semua data terkait berhasil dihapus.');
    }
}
