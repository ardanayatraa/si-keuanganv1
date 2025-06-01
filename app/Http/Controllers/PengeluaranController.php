<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\Rekening;
use App\Models\Anggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengeluaranController extends Controller
{
    public function index()
    {
        // eager load relasi kategori, pengguna, rekening
        $items = Pengeluaran::with('kategori', 'pengguna', 'rekening')->get();
        return view('pengeluaran.index', compact('items'));
    }

    public function create()
    {
        // ambil semua rekening untuk dropdown
        $rekenings = Rekening::all();
        return view('pengeluaran.create', compact('rekenings'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_pengguna' => 'required|string|exists:pengguna,id_pengguna',
            'id_rekening' => 'required|string|exists:rekening,id_rekening',
            'jumlah'      => 'required|numeric|min:0.01',
            'tanggal'     => 'required|date',
            'id_kategori' => 'required|string|exists:kategori_pengeluaran,id_kategori_pengeluaran',
            'deskripsi'   => 'nullable|string',
        ]);

        // 1) Cek anggaran: cari anggaran user + kategori di mana tanggal masuk dalam rentang
        $anggaran = Anggaran::where('id_pengguna', $data['id_pengguna'])
            ->where('id_kategori', $data['id_kategori'])
            ->where('periode_awal', '<=', $data['tanggal'])
            ->where('periode_akhir', '>=', $data['tanggal'])
            ->first();

        if ($anggaran) {
            // 2) Hitung total pengeluaran yang sudah ada untuk user+kategori di rentang tersebut
            $totalSudah = Pengeluaran::where('id_pengguna', $data['id_pengguna'])
                ->where('id_kategori', $data['id_kategori'])
                ->whereBetween('tanggal', [
                    $anggaran->periode_awal->toDateString(),
                    $anggaran->periode_akhir->toDateString()
                ])
                ->sum('jumlah');

            // 3) jika penambahan ini melebihi batas
            if (($totalSudah + $data['jumlah']) > $anggaran->jumlah_batas) {
                // redirect back dengan pesan error di field 'jumlah'
                return redirect()->back()
                    ->withErrors([
                        'jumlah' => "Total pengeluaran untuk kategori ini di periode "
                            . $anggaran->periode_awal->format('Y-m-d')
                            . "—" . $anggaran->periode_akhir->format('Y-m-d')
                            . " sudah Rp " . number_format($totalSudah, 2, ',', '.')
                            . ". Menambah Rp " . number_format($data['jumlah'], 2, ',', '.')
                            . " akan melebihi batas anggaran Rp "
                            . number_format($anggaran->jumlah_batas, 2, ',', '.') . "."
                    ])
                    ->withInput();
            }
        }
        // jika tidak ada anggaran yang relevan, skip pengecekan; tetap bisa disimpan

        DB::transaction(function() use ($data) {
            // 4) Buat record pengeluaran
            $p = Pengeluaran::create($data);

            // 5) Kurangi saldo rekening
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->decrement('saldo', $data['jumlah']);
        });

        return redirect()->route('pengeluaran.index')
                         ->with('success', 'Pengeluaran berhasil dibuat.');
    }

    public function show(Pengeluaran $pengeluaran)
    {
        return view('pengeluaran.show', compact('pengeluaran'));
    }

    public function edit(Pengeluaran $pengeluaran)
    {
        $rekenings = Rekening::all();
        return view('pengeluaran.edit', compact('pengeluaran', 'rekenings'));
    }

    public function update(Request $request, Pengeluaran $pengeluaran)
    {
        $data = $request->validate([
            'id_rekening' => 'required|string|exists:rekening,id_rekening',
            'jumlah'      => 'required|numeric|min:0.01',
            'tanggal'     => 'required|date',
            'id_kategori' => 'required|string|exists:kategori_pengeluaran,id_kategori_pengeluaran',
            'deskripsi'   => 'nullable|string',
        ]);

        // 1) Cek anggaran serupa di update: user dan kategori bisa diambil dari $pengeluaran
        $anggaran = Anggaran::where('id_pengguna', $pengeluaran->id_pengguna)
            ->where('id_kategori', $data['id_kategori'])
            ->where('periode_awal', '<=', $data['tanggal'])
            ->where('periode_akhir', '>=', $data['tanggal'])
            ->first();

        if ($anggaran) {
            // 2) Hitung total pengeluaran yang sudah ada untuk user+kategori di rentang tersebut (kecuali record ini sendiri)
            $totalSudah = Pengeluaran::where('id_pengguna', $pengeluaran->id_pengguna)
                ->where('id_kategori', $data['id_kategori'])
                ->whereBetween('tanggal', [
                    $anggaran->periode_awal->toDateString(),
                    $anggaran->periode_akhir->toDateString()
                ])
                ->where('id_pengeluaran', '!=', $pengeluaran->id_pengeluaran)
                ->sum('jumlah');

            // 3) jika update ini melebihi batas
            if (($totalSudah + $data['jumlah']) > $anggaran->jumlah_batas) {
                return redirect()->back()
                    ->withErrors([
                        'jumlah' => "Total pengeluaran untuk kategori ini di periode "
                            . $anggaran->periode_awal->format('Y-m-d')
                            . "—" . $anggaran->periode_akhir->format('Y-m-d')
                            . " sudah Rp " . number_format($totalSudah, 2, ',', '.')
                            . ". Mengubah menjadi Rp " . number_format($data['jumlah'], 2, ',', '.')
                            . " akan melebihi batas anggaran Rp "
                            . number_format($anggaran->jumlah_batas, 2, ',', '.') . "."
                    ])
                    ->withInput();
            }
        }
        // jika tidak ada anggaran yang relevan, skip pengecekan

        DB::transaction(function() use ($data, $pengeluaran) {
            // 4) "Refund" pengeluaran lama → tambahkan kembali saldo lama
            Rekening::where('id_rekening', $pengeluaran->id_rekening)
                   ->increment('saldo', $pengeluaran->jumlah);

            // 5) Update record pengeluaran
            $pengeluaran->update($data);

            // 6) Kurangi saldo berdasarkan nilai baru
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->decrement('saldo', $data['jumlah']);
        });

        return redirect()->route('pengeluaran.index')
                         ->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy(Pengeluaran $pengeluaran)
    {
        DB::transaction(function() use ($pengeluaran) {
            // 1) Tambahkan kembali saldo karena pengeluaran dihapus
            Rekening::where('id_rekening', $pengeluaran->id_rekening)
                   ->increment('saldo', $pengeluaran->jumlah);

            // 2) Hapus record pengeluaran
            $pengeluaran->delete();
        });

        return redirect()->route('pengeluaran.index')
                         ->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
