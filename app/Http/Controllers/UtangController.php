<?php

namespace App\Http\Controllers;

use App\Models\Utang;
use App\Models\Pemasukan;
use App\Models\Rekening;
use App\Models\KategoriPemasukan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UtangController extends Controller
{
    /**
     * Tampilkan daftar utang (bersama relasi pengguna & rekening).
     */
    public function index()
    {
        $items = Utang::with(['pengguna', 'rekening'])->get();
        return view('utang.index', compact('items'));
    }

    /**
     * Tampilkan form untuk menambah utang.
     */
    public function create()
    {
        $rekenings = Rekening::all();
        return view('utang.create', compact('rekenings'));
    }

    /**
     * Simpan utang baru.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            // 'id_pengguna' dihapus dari validasi
            'id_rekening'         => 'required|exists:rekening,id_rekening',
            'jumlah'              => 'required|numeric|min:0.01',
            'tanggal_pinjam'      => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_pinjam',
            'deskripsi'           => 'nullable|string',
        ]);

        // SET id_pengguna otomatis
        $data['id_pengguna'] = auth()->user()->id_pengguna;

        DB::transaction(function() use ($data) {
            // 1) Simpan record Utang
            $utang = Utang::create([
                'id_pengguna'         => $data['id_pengguna'],
                'id_rekening'         => $data['id_rekening'],
                'jumlah'              => $data['jumlah'],
                'tanggal_pinjam'      => $data['tanggal_pinjam'],
                'tanggal_jatuh_tempo' => $data['tanggal_jatuh_tempo'],
                'deskripsi'           => $data['deskripsi'] ?? null,
                'sisa_utang'          => $data['jumlah'],
                'status'              => 'belum dibayar',
            ]);

            // 2) Pastikan kategori "Utang" ada
          $kategoriUtang = KategoriPemasukan::where('id_pengguna', $data['id_pengguna'])
            ->where('nama_kategori', 'Utang')
            ->first();

// Jika belum ada, buat baru
        if (! $kategoriUtang) {
            $kategoriUtang = KategoriPemasukan::create([
                'id_pengguna'   => $data['id_pengguna'],
                'nama_kategori' => 'Utang',
                'deskripsi'     => 'Kategori untuk mencatat penerimaan utang',
                'icon'          => 'fas fa-hand-holding-usd',
            ]);
        }

            // 3) Catat Pemasukan
            Pemasukan::create([
                'id_pengguna' => $data['id_pengguna'],
                'jumlah'      => $data['jumlah'],
                'tanggal'     => $data['tanggal_pinjam'],
                'id_kategori' => $kategoriUtang->id_kategori_pemasukan,
                'deskripsi'   => 'Terima utang (ID ' . $utang->id_utang . ')',
                'id_rekening' => $data['id_rekening'],
            ]);

            // 4) Update saldo rekening
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->increment('saldo', $data['jumlah']);
        });

        return redirect()->route('utang.index')
                         ->with('success', 'Utang berhasil dicatat dan saldo rekening bertambah.');
    }

    /**
     * Tampilkan detail satu utang.
     */
    public function show(Utang $utang)
    {
        $utang->load(['pengguna', 'rekening']);
        return view('utang.show', compact('utang'));
    }

    /**
     * Tampilkan form edit utang.
     */
    public function edit(Utang $utang)
    {
        $rekenings = Rekening::all();
        return view('utang.edit', compact('utang', 'rekenings'));
    }

    /**
     * Perbarui data utang.
     */
    public function update(Request $request, Utang $utang)
    {
        $data = $request->validate([
            // 'id_pengguna' dihapus dari validasi
            'id_rekening'         => 'required|exists:rekening,id_rekening',
            'jumlah'              => 'required|numeric|min:0.01',
            'tanggal_pinjam'      => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_pinjam',
            'deskripsi'           => 'nullable|string',
        ]);

        // SET id_pengguna otomatis (meski tidak berubah)
        $data['id_pengguna'] = auth()->user()->id_pengguna;

        DB::transaction(function() use ($data, $utang) {
            // 1) Hapus & refund Pemasukan lama
            \App\Models\Pemasukan::where([
                ['deskripsi', 'like', '%Terima utang (ID ' . $utang->id_utang . ')%'],
                ['tanggal', '=', $utang->tanggal_pinjam],
                ['jumlah', '=', $utang->jumlah],
                ['id_rekening', '=', $utang->id_rekening],
            ])->delete();
            Rekening::where('id_rekening', $utang->id_rekening)
                   ->decrement('saldo', $utang->jumlah);

            // 2) Update Utang
            $utang->update([
                'id_pengguna'         => $data['id_pengguna'],
                'id_rekening'         => $data['id_rekening'],
                'jumlah'              => $data['jumlah'],
                'sisa_utang'          => $data['jumlah'],
                'tanggal_pinjam'      => $data['tanggal_pinjam'],
                'tanggal_jatuh_tempo' => $data['tanggal_jatuh_tempo'],
                'deskripsi'           => $data['deskripsi'] ?? null,
                'status'              => 'belum dibayar',
            ]);

            // 3) Pastikan kategori Utang
            $kategoriUtang = KategoriPemasukan::firstOrCreate(
                [
                    'id_pengguna'   => $data['id_pengguna'],
                    'nama_kategori' => 'Utang',
                ],
                [
                    'deskripsi' => 'Kategori untuk mencatat penerimaan utang',
                    'icon'      => 'fas fa-hand-holding-usd',
                ]
            );

            // 4) Buat ulang Pemasukan baru
            Pemasukan::create([
                'id_pengguna' => $data['id_pengguna'],
                'jumlah'      => $data['jumlah'],
                'tanggal'     => $data['tanggal_pinjam'],
                'id_kategori' => $kategoriUtang->id_kategori_pemasukan,
                'deskripsi'   => 'Terima utang (ID ' . $utang->id_utang . ')',
                'id_rekening' => $data['id_rekening'],
            ]);

            // 5) Tambah saldo rekening
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->increment('saldo', $data['jumlah']);
        });

        return redirect()->route('utang.index')
                         ->with('success', 'Utang berhasil diperbarui dan mutasi rekening disesuaikan.');
    }

    /**
     * Hapus satu utang.
     */
    public function destroy(Utang $utang)
    {
        DB::transaction(function() use ($utang) {
            // Hapus Pemasukan terkait & refund saldo
            \App\Models\Pemasukan::where([
                ['deskripsi', 'like', '%Terima utang (ID ' . $utang->id_utang . ')%'],
                ['tanggal', '=', $utang->tanggal_pinjam],
                ['jumlah', '=', $utang->jumlah],
                ['id_rekening', '=', $utang->id_rekening],
            ])->delete();
            Rekening::where('id_rekening', $utang->id_rekening)
                   ->decrement('saldo', $utang->jumlah);

            // Hapus Utang
            $utang->delete();
        });

        return redirect()->route('utang.index')
                         ->with('success', 'Utang berhasil dihapus dan saldo rekening dikembalikan.');
    }
}
