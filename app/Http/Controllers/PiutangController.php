<?php

namespace App\Http\Controllers;

use App\Models\Piutang;
use App\Models\Pengeluaran;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PiutangController extends Controller
{
    public function index()
    {
        $items = Piutang::with(['pengguna', 'rekening'])->get();
        return view('piutang.index', compact('items'));
    }

    public function create()
    {
        $rekenings = Rekening::all();
        return view('piutang.create', compact('rekenings'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_rekening'        => 'required|exists:rekening,id_rekening',
            'jumlah'             => 'required|numeric|min:0.01',
            'tanggal_pinjam'     => 'required|date',
            'tanggal_jatuh_tempo'=> 'required|date|after_or_equal:tanggal_pinjam',
            'deskripsi'          => 'nullable|string',
        ]);

        // ambil id_pengguna dari yang login
        $data['id_pengguna'] = auth()->user()->id_pengguna;

        DB::transaction(function() use ($data) {
            // 1) Catat pengeluaran—uang keluar dari rekening saat memberi pinjaman
            $pengeluaran = Pengeluaran::create([
                'id_pengguna'  => $data['id_pengguna'],
                'id_rekening'  => $data['id_rekening'],
                'jumlah'       => $data['jumlah'],
                'tanggal'      => $data['tanggal_pinjam'],
                'id_kategori'  => null,
                'deskripsi'    => 'Pinjamkan uang (Piutang)',
            ]);

            // 2) Kurangi saldo rekening
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->decrement('saldo', $data['jumlah']);

            // 3) Buat entri piutang
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

    public function show(Piutang $piutang)
    {
        $piutang->load(['pengguna', 'rekening']);
        return view('piutang.show', compact('piutang'));
    }

    public function edit(Piutang $piutang)
    {
        $rekenings = Rekening::all();
        return view('piutang.edit', compact('piutang', 'rekenings'));
    }

    public function update(Request $request, Piutang $piutang)
    {
        $data = $request->validate([
            'id_rekening'        => 'required|exists:rekening,id_rekening',
            'jumlah'             => 'required|numeric|min:0.01',
            'tanggal_pinjam'     => 'required|date',
            'tanggal_jatuh_tempo'=> 'required|date|after_or_equal:tanggal_pinjam',
            'deskripsi'          => 'nullable|string',
        ]);

        // ambil id_pengguna dari yang login
        $data['id_pengguna'] = auth()->user()->id_pengguna;

        DB::transaction(function() use ($data, $piutang) {
            // 1) Refund saldo rekening lama
            Rekening::where('id_rekening', $piutang->id_rekening)
                   ->increment('saldo', $piutang->jumlah);

            // 2) Hapus entri pengeluaran lama
            Pengeluaran::where([
                ['id_pengeluaran', '=', $piutang->id_pengeluaran],
            ])->delete();

            // 3) Update piutang (reset sisa dan status)
            $piutang->update([
                'id_pengguna'        => $data['id_pengguna'],
                'id_rekening'        => $data['id_rekening'],
                'jumlah'             => $data['jumlah'],
                'sisa_piutang'       => $data['jumlah'],
                'tanggal_pinjam'     => $data['tanggal_pinjam'],
                'tanggal_jatuh_tempo'=> $data['tanggal_jatuh_tempo'],
                'deskripsi'          => $data['deskripsi'] ?? null,
                'status'             => 'belum lunas',
                'id_pemasukan'       => null,
            ]);

            // 4) Buat ulang entri pengeluaran untuk piutang
            Pengeluaran::create([
                'id_pengguna'  => $data['id_pengguna'],
                'id_rekening'  => $data['id_rekening'],
                'jumlah'       => $data['jumlah'],
                'tanggal'      => $data['tanggal_pinjam'],
                'id_kategori'  => null,
                'deskripsi'    => 'Pinjamkan uang (Piutang)',
            ]);

            // 5) Kurangi saldo rekening kembali
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->decrement('saldo', $data['jumlah']);
        });

        return redirect()->route('piutang.index')
                         ->with('success', 'Piutang berhasil diperbarui dan mutasi rekening disesuaikan.');
    }

    public function destroy(Piutang $piutang)
    {
        DB::transaction(function() use ($piutang) {
            // 1) Refund saldo rekening
            Rekening::where('id_rekening', $piutang->id_rekening)
                   ->increment('saldo', $piutang->jumlah);

            // 2) Hapus entri pengeluaran terkait
            Pengeluaran::where([
                ['id_pengeluaran', '=', $piutang->id_pengeluaran],
            ])->delete();

            // 3) Hapus record piutang
            $piutang->delete();
        });

        return redirect()->route('piutang.index')
                         ->with('success', 'Piutang berhasil dihapus dan saldo rekening dikembalikan.');
    }
}
