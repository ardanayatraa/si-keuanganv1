<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\Rekening;
use App\Models\Anggaran;
use App\Models\KategoriPengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        // Ambil filter tanggal
        $start = $request->input('start_date');
        $end   = $request->input('end_date');

        $query = Pengeluaran::with(['kategori', 'rekening'])
            ->where('id_pengguna', Auth::user()->id_pengguna);

        if ($start) {
            $query->whereDate('tanggal', '>=', $start);
        }
        if ($end) {
            $query->whereDate('tanggal', '<=', $end);
        }

        $items = $query->orderBy('tanggal', 'desc')->get();

        return view('pengeluaran.index', compact('items', 'start', 'end'));
    }

    public function create()
    {
        $rekenings = Rekening::where('id_pengguna', Auth::user()->id_pengguna)->get();
        $kategoris = KategoriPengeluaran::where('id_pengguna', Auth::user()->id_pengguna)->get();

        return view('pengeluaran.create', compact('rekenings', 'kategoris'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_rekening'     => 'required|string|exists:rekening,id_rekening',
            'jumlah'          => 'required|numeric|min:0.01',
            'tanggal'         => 'required|date',
            'id_kategori'     => 'required|string|exists:kategori_pengeluaran,id_kategori_pengeluaran',
            'deskripsi'       => 'nullable|string',
            'bukti_transaksi' => 'nullable|image|max:2048',
        ]);

        $data['id_pengguna'] = Auth::user()->id_pengguna;

        if ($request->hasFile('bukti_transaksi')) {
            $data['bukti_transaksi'] = $request->file('bukti_transaksi')
                                             ->store('bukti_pengeluaran', 'public');
        }

        // Cek anggaran dan prepare warning
        $warning = null;
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
                    $anggaran->periode_akhir->toDateString(),
                ])
                ->sum('jumlah');

            if (($totalSudah + $data['jumlah']) > $anggaran->jumlah_batas) {
                $warning = "Peringatan: total pengeluaran periode {$anggaran->periode_awal->format('Y-m-d')}—{$anggaran->periode_akhir->format('Y-m-d')} sudah Rp " .
                           number_format($totalSudah,2,',','.') .
                           ". Dengan menambah Rp " . number_format($data['jumlah'],2,',','.') .
                           ", Anda akan melebihi batas Rp " . number_format($anggaran->jumlah_batas,2,',','.') . ".";
            }
        }

        DB::transaction(function() use ($data) {
            Pengeluaran::create($data);
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->decrement('saldo', $data['jumlah']);
        });

        if ($warning) {
            session()->flash('warning', $warning);
        }

        return redirect()->route('pengeluaran.index')
                         ->with('success', 'Pengeluaran berhasil dibuat.');
    }

    public function show($id)
    {
        $item = Pengeluaran::with(['kategori','rekening'])
            ->where('id_pengeluaran', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        return view('pengeluaran.show', compact('item'));
    }

    public function edit($id)
    {
        $pengeluaran = Pengeluaran::with(['kategori', 'rekening'])
            ->where('id_pengeluaran', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        $rekenings = Rekening::where('id_pengguna', Auth::user()->id_pengguna)->get();
        $kategoris = KategoriPengeluaran::where('id_pengguna', Auth::user()->id_pengguna)->get();

        return view('pengeluaran.edit', compact('pengeluaran', 'rekenings', 'kategoris'));
    }

    public function update(Request $request, $id)
    {
        $pengeluaran = Pengeluaran::where('id_pengeluaran', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        $data = $request->validate([
            'id_rekening'     => 'required|string|exists:rekening,id_rekening',
            'jumlah'          => 'required|numeric|min:0.01',
            'tanggal'         => 'required|date',
            'id_kategori'     => 'required|string|exists:kategori_pengeluaran,id_kategori_pengeluaran',
            'deskripsi'       => 'nullable|string',
            'bukti_transaksi' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('bukti_transaksi')) {
            if ($pengeluaran->bukti_transaksi) {
                Storage::disk('public')->delete($pengeluaran->bukti_transaksi);
            }
            $data['bukti_transaksi'] = $request->file('bukti_transaksi')
                                             ->store('bukti_pengeluaran', 'public');
        }

        // Cek anggaran (exclude current) dan prepare warning
        $warning = null;
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
                    $anggaran->periode_akhir->toDateString(),
                ])
                ->where('id_pengeluaran', '!=', $pengeluaran->id_pengeluaran)
                ->sum('jumlah');

            if (($totalSudah + $data['jumlah']) > $anggaran->jumlah_batas) {
                $warning = "Peringatan: total pengeluaran periode {$anggaran->periode_awal->format('Y-m-d')}—{$anggaran->periode_akhir->format('Y-m-d')} sudah Rp " .
                           number_format($totalSudah,2,',','.') .
                           ". Mengubah menjadi Rp " . number_format($data['jumlah'],2,',','.') .
                           " akan melebihi batas Rp " . number_format($anggaran->jumlah_batas,2,',','.') . ".";
            }
        }

        DB::transaction(function() use ($data, $pengeluaran) {
            // rollback saldo lama
            Rekening::where('id_rekening', $pengeluaran->id_rekening)
                   ->increment('saldo', $pengeluaran->jumlah);

            $pengeluaran->update($data);

            // kurangi saldo baru
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->decrement('saldo', $data['jumlah']);
        });

        if ($warning) {
            session()->flash('warning', $warning);
        }

        return redirect()->route('pengeluaran.index')
                         ->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pengeluaran = Pengeluaran::where('id_pengeluaran', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        DB::transaction(function() use ($pengeluaran) {
            // restore saldo
            Rekening::where('id_rekening', $pengeluaran->id_rekening)
                   ->increment('saldo', $pengeluaran->jumlah);

            if ($pengeluaran->bukti_transaksi) {
                Storage::disk('public')->delete($pengeluaran->bukti_transaksi);
            }

            $pengeluaran->delete();
        });

        return redirect()->route('pengeluaran.index')
                         ->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
