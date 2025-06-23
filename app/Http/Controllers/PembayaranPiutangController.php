<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPiutang;
use App\Models\Piutang;
use App\Models\Pemasukan;
use App\Models\Rekening;
use App\Models\KategoriPemasukan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembayaranPiutangController extends Controller
{
    /**
     * Tampilkan daftar pembayaran piutang milik user login.
     */
    public function index()
    {
        $items = PembayaranPiutang::with(['piutang.pengguna', 'pemasukan.rekening'])
            ->whereHas('piutang', fn($q) =>
                $q->where('id_pengguna', Auth::id())
            )
            ->orderBy('tanggal_pembayaran', 'desc')
            ->get();

        return view('piutang.pembayaran.index', compact('items'));
    }

    /**
     * Form tambah pembayaran piutang.
     */
    public function create()
    {
        $piutangs = Piutang::where('id_pengguna', Auth::id())
                          ->where('status', 'belum lunas')
                          ->with('pengguna')
                          ->get();

        $rekenings = Rekening::where('id_pengguna', Auth::id())->get();

        return view('piutang.pembayaran.create', compact('piutangs', 'rekenings'));
    }

    /**
     * Simpan pembayaran piutang baru.
     */
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
            $piutang = Piutang::where('id_piutang', $data['id_piutang'])
                              ->where('id_pengguna', Auth::id())
                              ->firstOrFail();

            // Pastikan kategori "Pelunasan Piutang" ada
            $kat = KategoriPemasukan::firstOrCreate(
                [
                    'id_pengguna'   => $piutang->id_pengguna,
                    'nama_kategori' => 'Pelunasan Piutang',
                ],
                [
                    'deskripsi' => 'Pencatatan pelunasan piutang',
                    'icon'      => 'fas fa-handshake',
                ]
            );

            // Buat pemasukan
            $pemasukan = Pemasukan::create([
                'id_pengguna' => $piutang->id_pengguna,
                'jumlah'      => $data['jumlah_dibayar'],
                'tanggal'     => $data['tanggal_pembayaran'],
                'id_kategori' => $kat->id_kategori_pemasukan,
                'deskripsi'   => 'Pelunasan Piutang ID ' . $piutang->id_piutang,
                'id_rekening' => $data['id_rekening'],
            ]);

            // Tambah saldo rekening
            Rekening::find($data['id_rekening'])
                   ->increment('saldo', $data['jumlah_dibayar']);

            // Kurangi sisa piutang
            $piutang->sisa_piutang -= $data['jumlah_dibayar'];
            if ($piutang->sisa_piutang <= 0) {
                $piutang->sisa_piutang = 0;
                $piutang->status       = 'lunas';
            }
            $piutang->save();

            // Simpan pembayaran piutang
            PembayaranPiutang::create([
                'id_piutang'         => $piutang->id_piutang,
                'id_pemasukan'       => $pemasukan->id_pemasukan,
                'id_rekening'        => $data['id_rekening'],
                'jumlah_dibayar'     => $data['jumlah_dibayar'],
                'tanggal_pembayaran' => $data['tanggal_pembayaran'],
                'metode_pembayaran'  => $data['metode_pembayaran'] ?? null,
                'deskripsi'          => $data['deskripsi'] ?? null,
            ]);

            // Jika sudah lunas, hapus record piutang agar tak muncul lagi
            if ($piutang->status === 'lunas') {
                $piutang->delete();
            }
        });

        return redirect()
            ->route('piutang.pembayaran.index')
            ->with('success', 'Pembayaran piutang berhasil dibuat.');
    }

    /**
     * Tampilkan detail pembayaran (pastikan milik user).
     */
    public function show($id)
    {
        $p = PembayaranPiutang::with(['piutang.pengguna', 'pemasukan.rekening'])
            ->where('id_pembayaran_piutang', $id)
            ->whereHas('piutang', fn($q) =>
                $q->where('id_pengguna', Auth::id())
            )
            ->firstOrFail();

        return view('piutang.pembayaran.show', ['pembayaran' => $p]);
    }

    /**
     * Form edit pembayaran (pastikan milik user).
     */
    public function edit($id)
    {
        $p = PembayaranPiutang::with(['piutang', 'pemasukan.rekening'])
            ->where('id_pembayaran_piutang', $id)
            ->whereHas('piutang', fn($q) =>
                $q->where('id_pengguna', Auth::id())
            )
            ->firstOrFail();

        $piutangs = Piutang::where('id_pengguna', Auth::id())
                          ->where(fn($q) =>
                              $q->where('status', 'belum lunas')
                                ->orWhere('id_piutang', $p->id_piutang)
                          )
                          ->with('pengguna')
                          ->get();

        $rekenings = Rekening::where('id_pengguna', Auth::id())->get();

        return view('piutang.pembayaran.edit', compact('p', 'piutangs', 'rekenings'));
    }

    /**
     * Update pembayaran piutang.
     */
    public function update(Request $request, $id)
    {
        $p = PembayaranPiutang::where('id_pembayaran_piutang', $id)
            ->whereHas('piutang', fn($q) =>
                $q->where('id_pengguna', Auth::id())
            )
            ->firstOrFail();

        $data = $request->validate([
            'id_piutang'         => 'required|exists:piutang,id_piutang',
            'id_rekening'        => 'required|exists:rekening,id_rekening',
            'jumlah_dibayar'     => 'required|numeric|min:0.01',
            'tanggal_pembayaran' => 'required|date',
            'metode_pembayaran'  => 'nullable|string',
            'deskripsi'          => 'nullable|string',
        ]);

        DB::transaction(function() use ($data, $p) {
            // rollback saldo & sisa_piutang lama
            $oldMasuk = $p->pemasukan;
            Rekening::find($oldMasuk->id_rekening)
                   ->decrement('saldo', $oldMasuk->jumlah);
            $piutangOld = $p->piutang;
            $piutangOld->sisa_piutang += $p->jumlah_dibayar;
            $piutangOld->status = $piutangOld->sisa_piutang > 0 ? 'belum lunas' : 'lunas';
            $piutangOld->save();
            $oldMasuk->delete();

            // kategori
            $kat = KategoriPemasukan::firstOrCreate(
                ['id_pengguna'=>Auth::id(),'nama_kategori'=>'Pelunasan Piutang'],
                ['deskripsi'=>'Pencatatan pelunasan piutang','icon'=>'fas fa-handshake']
            );

            // buat pemasukan baru
            $newMasuk = Pemasukan::create([
                'id_pengguna' => $piutangOld->id_pengguna,
                'jumlah'      => $data['jumlah_dibayar'],
                'tanggal'     => $data['tanggal_pembayaran'],
                'id_kategori' => $kat->id_kategori_pemasukan,
                'deskripsi'   => 'Pelunasan Piutang ID ' . $piutangOld->id_piutang,
                'id_rekening' => $data['id_rekening'],
            ]);

            Rekening::find($data['id_rekening'])
                   ->increment('saldo', $data['jumlah_dibayar']);

            // kurangi sisa_piutang baru
            $piutangNew = $piutangOld->fresh();
            $piutangNew->sisa_piutang -= $data['jumlah_dibayar'];
            if ($piutangNew->sisa_piutang <= 0) {
                $piutangNew->sisa_piutang = 0;
                $piutangNew->status       = 'lunas';
            }
            $piutangNew->save();

            // update payment record
            $p->update([
                'id_piutang'         => $piutangNew->id_piutang,
                'id_pemasukan'       => $newMasuk->id_pemasukan,
                'id_rekening'        => $data['id_rekening'],
                'jumlah_dibayar'     => $data['jumlah_dibayar'],
                'tanggal_pembayaran' => $data['tanggal_pembayaran'],
                'metode_pembayaran'  => $data['metode_pembayaran'] ?? null,
                'deskripsi'          => $data['deskripsi'] ?? null,
            ]);

            // jika lunas, hapus piutang
            if ($piutangNew->status === 'lunas') {
                $piutangNew->delete();
            }
        });

        return redirect()
            ->route('piutang.pembayaran.index')
            ->with('success', 'Pembayaran piutang berhasil diperbarui.');
    }

    /**
     * Hapus pembayaran piutang.
     */
    public function destroy($id)
    {
        $p = PembayaranPiutang::where('id_pembayaran_piutang', $id)
            ->whereHas('piutang', fn($q) =>
                $q->where('id_pengguna', Auth::id())
            )
            ->firstOrFail();

        DB::transaction(function() use ($p) {
            $masuk  = $p->pemasukan;
            Rekening::find($masuk->id_rekening)
                   ->decrement('saldo', $masuk->jumlah);

            $piutang = $p->piutang;
            $piutang->sisa_piutang += $p->jumlah_dibayar;
            $piutang->status = $piutang->sisa_piutang > 0 ? 'belum lunas' : 'lunas';
            $piutang->save();

            $masuk->delete();
            $p->delete();
        });

        return redirect()
            ->route('piutang.pembayaran.index')
            ->with('success', 'Pembayaran piutang berhasil dihapus.');
    }
}
