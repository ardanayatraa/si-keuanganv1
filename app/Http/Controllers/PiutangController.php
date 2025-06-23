<?php

namespace App\Http\Controllers;

use App\Models\Piutang;
use App\Models\Pengeluaran;
use App\Models\Rekening;
use App\Models\KategoriPengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PiutangController extends Controller
{
    /**
     * Daftar piutang milik user login.
     */
    public function index()
    {
        $items = Piutang::with(['pengguna', 'rekening'])
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->orderBy('tanggal_pinjam', 'desc')
            ->get();

        return view('piutang.index', compact('items'));
    }

    /**
     * Form tambah piutang. Rekening hanya milik user.
     */
    public function create()
    {
        $rekenings = Rekening::where('id_pengguna', Auth::user()->id_pengguna)
            ->get();

        return view('piutang.create', compact('rekenings'));
    }

    /**
     * Simpan piutang baru: catat pengeluaran + buat piutang.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_rekening'         => 'required|exists:rekening,id_rekening',
            'jumlah'              => 'required|numeric|min:0.01',
            'tanggal_pinjam'      => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_pinjam',
            'deskripsi'           => 'nullable|string',
        ]);

        $data['id_pengguna'] = Auth::user()->id_pengguna;

        DB::transaction(function() use ($data) {
            // 1) Pastikan kategori "Piutang" ada
            $kategori = KategoriPengeluaran::firstOrCreate(
                [
                    'id_pengguna'   => $data['id_pengguna'],
                    'nama_kategori' => 'Piutang',
                ],
                [
                    'deskripsi' => 'Kategori untuk pengeluaran pinjaman (Piutang)',
                    'icon'      => 'fas fa-handshake',
                ]
            );

            // 2) Catat pengeluaran keluar
            Pengeluaran::create([
                'id_pengguna' => $data['id_pengguna'],
                'id_rekening' => $data['id_rekening'],
                'jumlah'      => $data['jumlah'],
                'tanggal'     => $data['tanggal_pinjam'],
                'id_kategori' => $kategori->id_kategori_pengeluaran,
                'deskripsi'   => 'Pinjamkan uang (Piutang)',
            ]);

            // 3) Kurangi saldo rekening
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->decrement('saldo', $data['jumlah']);

            // 4) Buat entri piutang
            Piutang::create([
                'id_pengguna'        => $data['id_pengguna'],
                'id_rekening'        => $data['id_rekening'],
                'id_pemasukan'       => null,
                'jumlah'             => $data['jumlah'],
                'sisa_piutang'       => $data['jumlah'],
                'tanggal_pinjam'     => $data['tanggal_pinjam'],
                'tanggal_jatuh_tempo'=> $data['tanggal_jatuh_tempo'],
                'deskripsi'          => $data['deskripsi'] ?? null,
                'status'             => 'belum lunas',
            ]);
        });

        return redirect()->route('piutang.index')
                         ->with('success', 'Piutang berhasil dicatat dan saldo rekening terpotong.');
    }

    /**
     * Detail piutang (pastikan milik user).
     */
    public function show($id)
    {
        $piutang = Piutang::with(['pengguna', 'rekening'])
            ->where('id_piutang', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        return view('piutang.show', compact('piutang'));
    }

    /**
     * Form edit piutang (pastikan milik user).
     */
    public function edit($id)
    {
        $piutang = Piutang::where('id_piutang', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        $rekenings = Rekening::where('id_pengguna', Auth::user()->id_pengguna)
            ->get();

        return view('piutang.edit', compact('piutang', 'rekenings'));
    }

    /**
     * Update piutang, rollback & recreate pengeluaran, reset sisa.
     */
    public function update(Request $request, $id)
    {
        $piutang = Piutang::where('id_piutang', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        $data = $request->validate([
            'id_rekening'         => 'required|exists:rekening,id_rekening',
            'jumlah'              => 'required|numeric|min:0.01',
            'tanggal_pinjam'      => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_pinjam',
            'deskripsi'           => 'nullable|string',
        ]);

        $data['id_pengguna'] = Auth::user()->id_pengguna;

        DB::transaction(function() use ($data, $piutang) {
            // Refund saldo lama
            Rekening::where('id_rekening', $piutang->id_rekening)
                   ->increment('saldo', $piutang->jumlah);

            // Hapus pengeluaran lama
            Pengeluaran::where('id_pengeluaran', $piutang->id_pengeluaran)
                       ->delete();

            // Reset data piutang
            $piutang->update([
                'id_rekening'        => $data['id_rekening'],
                'jumlah'             => $data['jumlah'],
                'sisa_piutang'       => $data['jumlah'],
                'tanggal_pinjam'     => $data['tanggal_pinjam'],
                'tanggal_jatuh_tempo'=> $data['tanggal_jatuh_tempo'],
                'deskripsi'          => $data['deskripsi'] ?? null,
                'status'             => 'belum lunas',
                'id_pemasukan'       => null,
            ]);

            // Pastikan kategori "Piutang"
            $kategori = KategoriPengeluaran::firstOrCreate(
                [
                    'id_pengguna'   => $data['id_pengguna'],
                    'nama_kategori' => 'Piutang',
                ],
                [
                    'deskripsi' => 'Kategori untuk pengeluaran pinjaman (Piutang)',
                    'icon'      => 'fas fa-handshake',
                ]
            );

            // Buat ulang pengeluaran baru
            Pengeluaran::create([
                'id_pengguna' => $data['id_pengguna'],
                'id_rekening' => $data['id_rekening'],
                'jumlah'      => $data['jumlah'],
                'tanggal'     => $data['tanggal_pinjam'],
                'id_kategori' => $kategori->id_kategori_pengeluaran,
                'deskripsi'   => 'Pinjamkan uang (Piutang)',
            ]);

            // Kurangi saldo baru
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->decrement('saldo', $data['jumlah']);
        });

        return redirect()->route('piutang.index')
                         ->with('success', 'Piutang berhasil diperbarui dan mutasi rekening disesuaikan.');
    }

    /**
     * Hapus piutang (refund & delete).
     */
    public function destroy($id)
    {
        $piutang = Piutang::where('id_piutang', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        DB::transaction(function() use ($piutang) {
            // Refund saldo
            Rekening::where('id_rekening', $piutang->id_rekening)
                   ->increment('saldo', $piutang->jumlah);

            // Hapus pengeluaran terkait
            Pengeluaran::where('id_pengeluaran', $piutang->id_pengeluaran)
                       ->delete();

            // Hapus record piutang
            $piutang->delete();
        });

        return redirect()->route('piutang.index')
                         ->with('success', 'Piutang berhasil dihapus dan saldo rekening dikembalikan.');
    }
}
