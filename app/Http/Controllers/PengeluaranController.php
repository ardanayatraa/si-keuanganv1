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
            // hapus id_pengguna dari validasi
            'id_rekening' => 'required|string|exists:rekening,id_rekening',
            'jumlah'      => 'required|numeric|min:0.01',
            'tanggal'     => 'required|date',
            'id_kategori' => 'required|string|exists:kategori_pengeluaran,id_kategori_pengeluaran',
            'deskripsi'   => 'nullable|string',
        ]);

        // SET id_pengguna otomatis dari yang login
        $data['id_pengguna'] = auth()->user()->id_pengguna;

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

        DB::transaction(function() use ($data, $pengeluaran) {
            // refund saldo lama
            Rekening::where('id_rekening', $pengeluaran->id_rekening)
                   ->increment('saldo', $pengeluaran->jumlah);

            // update pengeluaran
            $pengeluaran->update($data);

            // kurangi saldo baru
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->decrement('saldo', $data['jumlah']);
        });

        return redirect()->route('pengeluaran.index')
                         ->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy(Pengeluaran $pengeluaran)
    {
        DB::transaction(function() use ($pengeluaran) {
            // kembalikan saldo sebelum hapus
            Rekening::where('id_rekening', $pengeluaran->id_rekening)
                   ->increment('saldo', $pengeluaran->jumlah);

            // hapus record
            $pengeluaran->delete();
        });

        return redirect()->route('pengeluaran.index')
                         ->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
