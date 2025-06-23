<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\Rekening;
use App\Models\Anggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengeluaranController extends Controller
{
    /**
     * Daftar pengeluaran milik user login.
     */
    public function index()
    {
        $items = Pengeluaran::with(['kategori', 'rekening'])
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('pengeluaran.index', compact('items'));
    }

    /**
     * Form tambah pengeluaran. Rekening hanya milik user.
     */
    public function create()
    {
        $rekenings = Rekening::where('id_pengguna', Auth::user()->id_pengguna)
            ->get();

        return view('pengeluaran.create', compact('rekenings'));
    }

    /**
     * Simpan pengeluaran baru, cek anggaran, kurangi saldo.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_rekening' => 'required|string|exists:rekening,id_rekening',
            'jumlah'      => 'required|numeric|min:0.01',
            'tanggal'     => 'required|date',
            'id_kategori' => 'required|string|exists:kategori_pengeluaran,id_kategori_pengeluaran',
            'deskripsi'   => 'nullable|string',
        ]);

        $data['id_pengguna'] = Auth::user()->id_pengguna;

        // Cek anggaran
        $anggaran = Anggaran::where('id_pengguna', $data['id_pengguna'])
            ->where('id_kategori', $data['id_kategori'])
            ->where('periode_awal', '<=', $data['tanggal'])
            ->where('periode_akhir', '>=', $data['tanggal'])
            ->first();

        if ($anggaran) {
            $totalSudah = Pengeluaran::where('id_pengguna', $data['id_pengguna'])
                ->where('id_kategori', $data['id_kategori'])
                ->whereBetween('tanggal', [
                    $anggaran->periode_awal->toDateString(),
                    $anggaran->periode_akhir->toDateString()
                ])
                ->sum('jumlah');

            if (($totalSudah + $data['jumlah']) > $anggaran->jumlah_batas) {
                return redirect()->back()
                    ->withErrors(['jumlah' =>
                        "Total pengeluaran untuk kategori ini di periode "
                        . $anggaran->periode_awal->format('Y-m-d') . "—"
                        . $anggaran->periode_akhir->format('Y-m-d')
                        . " sudah Rp " . number_format($totalSudah, 2, ',', '.')
                        . ". Menambah Rp " . number_format($data['jumlah'], 2, ',', '.')
                        . " akan melebihi batas Rp "
                        . number_format($anggaran->jumlah_batas, 2, ',', '.') . "."
                    ])
                    ->withInput();
            }
        }

        DB::transaction(function() use ($data) {
            // buat pengeluaran
            $p = Pengeluaran::create($data);
            // kurangi saldo rekening
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->decrement('saldo', $data['jumlah']);
        });

        return redirect()->route('pengeluaran.index')
                         ->with('success', 'Pengeluaran berhasil dibuat.');
    }

    /**
     * Detail pengeluaran (pastikan milik user).
     */
    public function show($id)
    {
        $pengeluaran = Pengeluaran::with(['kategori', 'rekening'])
            ->where('id_pengeluaran', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        return view('pengeluaran.show', compact('pengeluaran'));
    }

    /**
     * Form edit pengeluaran (pastikan milik user).
     */
    public function edit($id)
    {
        $pengeluaran = Pengeluaran::where('id_pengeluaran', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        $rekenings = Rekening::where('id_pengguna', Auth::user()->id_pengguna)
            ->get();

        return view('pengeluaran.edit', compact('pengeluaran', 'rekenings'));
    }

    /**
     * Update pengeluaran, cek anggaran, dan adjust saldo.
     */
    public function update(Request $request, $id)
    {
        $pengeluaran = Pengeluaran::where('id_pengeluaran', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        $data = $request->validate([
            'id_rekening' => 'required|string|exists:rekening,id_rekening',
            'jumlah'      => 'required|numeric|min:0.01',
            'tanggal'     => 'required|date',
            'id_kategori' => 'required|string|exists:kategori_pengeluaran,id_kategori_pengeluaran',
            'deskripsi'   => 'nullable|string',
        ]);

        // Cek anggaran
        $anggaran = Anggaran::where('id_pengguna', $pengeluaran->id_pengguna)
            ->where('id_kategori', $data['id_kategori'])
            ->where('periode_awal', '<=', $data['tanggal'])
            ->where('periode_akhir', '>=', $data['tanggal'])
            ->first();

        if ($anggaran) {
            $totalSudah = Pengeluaran::where('id_pengguna', $pengeluaran->id_pengguna)
                ->where('id_kategori', $data['id_kategori'])
                ->whereBetween('tanggal', [
                    $anggaran->periode_awal->toDateString(),
                    $anggaran->periode_akhir->toDateString()
                ])
                ->where('id_pengeluaran', '!=', $pengeluaran->id_pengeluaran)
                ->sum('jumlah');

            if (($totalSudah + $data['jumlah']) > $anggaran->jumlah_batas) {
                return redirect()->back()
                    ->withErrors(['jumlah' =>
                        "Total pengeluaran untuk kategori ini di periode "
                        . $anggaran->periode_awal->format('Y-m-d') . "—"
                        . $anggaran->periode_akhir->format('Y-m-d')
                        . " sudah Rp " . number_format($totalSudah, 2, ',', '.')
                        . ". Mengubah menjadi Rp " . number_format($data['jumlah'], 2, ',', '.')
                        . " akan melebihi batas Rp "
                        . number_format($anggaran->jumlah_batas, 2, ',', '.') . "."
                    ])
                    ->withInput();
            }
        }

        DB::transaction(function() use ($data, $pengeluaran) {
            // refund saldo lama
            Rekening::where('id_rekening', $pengeluaran->id_rekening)
                   ->increment('saldo', $pengeluaran->jumlah);

            // update
            $pengeluaran->update($data);

            // kurangi saldo baru
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->decrement('saldo', $data['jumlah']);
        });

        return redirect()->route('pengeluaran.index')
                         ->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    /**
     * Hapus pengeluaran (kembalikan saldo dan delete).
     */
    public function destroy($id)
    {
        $pengeluaran = Pengeluaran::where('id_pengeluaran', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        DB::transaction(function() use ($pengeluaran) {
            // kembalikan saldo
            Rekening::where('id_rekening', $pengeluaran->id_rekening)
                   ->increment('saldo', $pengeluaran->jumlah);

            // hapus
            $pengeluaran->delete();
        });

        return redirect()->route('pengeluaran.index')
                         ->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
