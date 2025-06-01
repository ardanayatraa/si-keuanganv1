<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPiutang;
use App\Models\Piutang;
use App\Models\Pemasukan;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranPiutangController extends Controller
{
    public function index()
    {
        $items = PembayaranPiutang::with([
            'piutang.pengguna',
            'pemasukan.rekening'
        ])->get();

        return view('pembayaran-piutang.index', compact('items'));
    }

    public function create()
    {
        // Ambil daftar piutang yang belum lunas dan daftar rekening
        $piutangs = Piutang::where('status', 'belum lunas')
                          ->with('pengguna')
                          ->get();
        $rekenings = Rekening::all();
        return view('pembayaran-piutang.create', compact('piutangs', 'rekenings'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_piutang'         => 'required|exists:piutang,id_piutang',
            'id_rekening'        => 'required|exists:rekening,id_rekening',
            'jumlah_dibayar'     => 'required|numeric|min:0.01',
            'tanggal_pembayaran' => 'required|date',
            'metode_pembayaran'  => 'nullable|string',
            'deskripsi'          => 'nullable|string',
        ]);

        DB::transaction(function() use ($data) {
            // 1) Ambil entitas Piutang
            $piutang = Piutang::findOrFail($data['id_piutang']);

            // 2) Masukkan Pemasukan: mencatat uang masuk dari pelunasan piutang
            $pemasukan = Pemasukan::create([
                'id_pengguna'  => $piutang->id_pengguna,
                'jumlah'       => $data['jumlah_dibayar'],
                'tanggal'      => $data['tanggal_pembayaran'],
                'id_kategori'  => null, // atau kategori khusus “Pelunasan Piutang”
                'deskripsi'    => 'Pelunasan Piutang ID ' . $piutang->id_piutang,
                'id_rekening'  => $data['id_rekening'],
            ]);

            // 3) Tambah saldo rekening
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->increment('saldo', $data['jumlah_dibayar']);

            // 4) Update sisa_piutang dan status
            $piutang->sisa_piutang -= $data['jumlah_dibayar'];
            if ($piutang->sisa_piutang <= 0) {
                $piutang->sisa_piutang = 0;
                $piutang->status = 'lunas';
            }
            $piutang->save();

            // 5) Simpan record PembayaranPiutang
            PembayaranPiutang::create([
                'id_piutang'        => $data['id_piutang'],
                'id_pemasukan'      => $pemasukan->id_pemasukan,
                'id_rekening'       => $data['id_rekening'],
                'jumlah_dibayar'    => $data['jumlah_dibayar'],
                'tanggal_pembayaran'=> $data['tanggal_pembayaran'],
                'metode_pembayaran' => $data['metode_pembayaran'] ?? null,
                'deskripsi'         => $data['deskripsi'] ?? null,
            ]);
        });

        return redirect()
            ->route('pembayaran-piutang.index')
            ->with('success', 'Pembayaran piutang berhasil dibuat.');
    }

    public function show(PembayaranPiutang $pembayaranPiutang)
    {
        $pembayaranPiutang->load(['piutang.pengguna', 'pemasukan.rekening']);
        return view('pembayaran-piutang.show', compact('pembayaranPiutang'));
    }

    public function edit(PembayaranPiutang $pembayaranPiutang)
    {
        $pembayaranPiutang->load(['piutang', 'pemasukan.rekening']);
        $piutangs = Piutang::where('status', 'belum lunas')
                          ->orWhere('id_piutang', $pembayaranPiutang->id_piutang)
                          ->with('pengguna')
                          ->get();
        $rekenings = Rekening::all();
        return view('pembayaran-piutang.edit', compact('pembayaranPiutang', 'piutangs', 'rekenings'));
    }

    public function update(Request $request, PembayaranPiutang $pembayaranPiutang)
    {
        $data = $request->validate([
            'id_piutang'         => 'required|exists:piutang,id_piutang',
            'id_rekening'        => 'required|exists:rekening,id_rekening',
            'jumlah_dibayar'     => 'required|numeric|min:0.01',
            'tanggal_pembayaran' => 'required|date',
            'metode_pembayaran'  => 'nullable|string',
            'deskripsi'          => 'nullable|string',
        ]);

        DB::transaction(function() use ($data, $pembayaranPiutang) {
            // 1) Revert efek pembayaran lama
            $oldPiutang    = $pembayaranPiutang->piutang;
            $oldPemasukan  = $pembayaranPiutang->pemasukan;
            $oldRekeningId = $oldPemasukan->id_rekening;
            $oldJumlah     = $pembayaranPiutang->jumlah_dibayar;

            // a) Kurangi saldo rekening lama (membalikkan pemasukan lama)
            Rekening::where('id_rekening', $oldRekeningId)
                   ->decrement('saldo', $oldJumlah);

            // b) Tambah kembali sisa_piutang
            $oldPiutang->sisa_piutang += $oldJumlah;
            if ($oldPiutang->sisa_piutang > 0) {
                $oldPiutang->status = 'belum lunas';
            }
            $oldPiutang->save();

            // c) Hapus Pemasukan lama
            $oldPemasukan->delete();

            // 2) Proses pembayaran baru
            $newPiutang = Piutang::findOrFail($data['id_piutang']);
            // Buat entri Pemasukan baru
            $newPemasukan = Pemasukan::create([
                'id_pengguna'  => $newPiutang->id_pengguna,
                'jumlah'       => $data['jumlah_dibayar'],
                'tanggal'      => $data['tanggal_pembayaran'],
                'id_kategori'  => null,
                'deskripsi'    => 'Pelunasan Piutang ID ' . $newPiutang->id_piutang,
                'id_rekening'  => $data['id_rekening'],
            ]);
            // Tambah saldo rekening baru
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->increment('saldo', $data['jumlah_dibayar']);

            // Update sisa_piutang dan status pada piutang baru
            $newPiutang->sisa_piutang -= $data['jumlah_dibayar'];
            if ($newPiutang->sisa_piutang <= 0) {
                $newPiutang->sisa_piutang = 0;
                $newPiutang->status = 'lunas';
            }
            $newPiutang->save();

            // 3) Update record PembayaranPiutang
            $pembayaranPiutang->update([
                'id_piutang'        => $data['id_piutang'],
                'id_pemasukan'      => $newPemasukan->id_pemasukan,
                'id_rekening'       => $data['id_rekening'],
                'jumlah_dibayar'    => $data['jumlah_dibayar'],
                'tanggal_pembayaran'=> $data['tanggal_pembayaran'],
                'metode_pembayaran' => $data['metode_pembayaran'] ?? null,
                'deskripsi'         => $data['deskripsi'] ?? null,
            ]);
        });

        return redirect()
            ->route('pembayaran-piutang.index')
            ->with('success', 'Pembayaran piutang berhasil diperbarui.');
    }

    public function destroy(PembayaranPiutang $pembayaranPiutang)
    {
        DB::transaction(function() use ($pembayaranPiutang) {
            $piutang    = $pembayaranPiutang->piutang;
            $pemasukan  = $pembayaranPiutang->pemasukan;
            $rekeningId = $pemasukan->id_rekening;
            $jumlah     = $pembayaranPiutang->jumlah_dibayar;

            // 1) Kurangi saldo rekening (revert pemasukan)
            Rekening::where('id_rekening', $rekeningId)
                   ->decrement('saldo', $jumlah);

            // 2) Tambah kembali sisa_piutang
            $piutang->sisa_piutang += $jumlah;
            if ($piutang->sisa_piutang > 0) {
                $piutang->status = 'belum lunas';
            }
            $piutang->save();

            // 3) Hapus record Pemasukan
            $pemasukan->delete();

            // 4) Hapus record PembayaranPiutang
            $pembayaranPiutang->delete();
        });

        return redirect()
            ->route('pembayaran-piutang.index')
            ->with('success', 'Pembayaran piutang berhasil dihapus dan mutasi rekening dikembalikan.');
    }
}
