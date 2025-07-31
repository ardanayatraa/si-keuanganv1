<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use App\Models\Rekening;
use App\Models\KategoriPemasukan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PemasukanController extends Controller
{
    public function index(Request $request)
    {
        // Ambil filter tanggal jika ada
        $start = $request->input('start_date');
        $end   = $request->input('end_date');

        $query = Pemasukan::with(['kategori', 'rekening'])
            ->where('id_pengguna', Auth::user()->id_pengguna);

        if ($start) {
            $query->whereDate('tanggal', '>=', $start);
        }
        if ($end) {
            $query->whereDate('tanggal', '<=', $end);
        }

        $items = $query
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('pemasukan.index', compact('items', 'start', 'end'));
    }

    public function create()
    {
        $rekenings = Rekening::where('id_pengguna', Auth::user()->id_pengguna)->get();
        $kategoris = KategoriPemasukan::where('id_pengguna', Auth::user()->id_pengguna)->get();

        return view('pemasukan.create', compact('rekenings', 'kategoris'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_rekening'     => 'required|string|exists:rekening,id_rekening',
            'jumlah'          => 'required|numeric|min:0.01',
            'tanggal'         => 'required|date',
            'id_kategori'     => 'required|string|exists:kategori_pemasukan,id_kategori_pemasukan',
            'deskripsi'       => 'nullable|string',
            'bukti_transaksi' => 'nullable|image|max:2048',
        ]);

        $data['id_pengguna'] = Auth::user()->id_pengguna;

        // Simpan file bukti transaksi jika ada
        if ($request->hasFile('bukti_transaksi')) {
            $data['bukti_transaksi'] = $request->file('bukti_transaksi')
                                             ->store('bukti_pemasukan', 'public');
        }

        DB::transaction(function() use ($data) {
            Pemasukan::create($data);
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->increment('saldo', $data['jumlah']);
        });

        return redirect()->route('pemasukan.index')
                         ->with('success', 'Pemasukan berhasil dibuat.');
    }

    public function show($id)
    {
        $pemasukan = Pemasukan::with(['kategori', 'rekening'])
            ->where('id_pemasukan', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        return view('pemasukan.show', compact('pemasukan'));
    }

    public function edit($id)
    {
        $pemasukan = Pemasukan::where('id_pemasukan', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        $rekenings = Rekening::where('id_pengguna', Auth::user()->id_pengguna)->get();
        $kategoris = KategoriPemasukan::where('id_pengguna', Auth::user()->id_pengguna)->get();

        return view('pemasukan.edit', compact('pemasukan', 'rekenings', 'kategoris'));
    }

    public function update(Request $request, $id)
    {
        $pemasukan = Pemasukan::where('id_pemasukan', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        $data = $request->validate([
            'id_rekening'     => 'required|string|exists:rekening,id_rekening',
            'jumlah'          => 'required|numeric|min:0.01',
            'tanggal'         => 'required|date',
            'id_kategori'     => 'required|string|exists:kategori_pemasukan,id_kategori_pemasukan',
            'deskripsi'       => 'nullable|string',
            'bukti_transaksi' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('bukti_transaksi')) {
            if ($pemasukan->bukti_transaksi) {
                Storage::disk('public')->delete($pemasukan->bukti_transaksi);
            }
            $data['bukti_transaksi'] = $request->file('bukti_transaksi')
                                             ->store('bukti_pemasukan', 'public');
        }

        DB::transaction(function() use ($data, $pemasukan) {
            // rollback saldo lama
            Rekening::where('id_rekening', $pemasukan->id_rekening)
                   ->decrement('saldo', $pemasukan->jumlah);

            // update data
            $pemasukan->update($data);

            // tambah saldo baru
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->increment('saldo', $data['jumlah']);
        });

        return redirect()->route('pemasukan.index')
                         ->with('success', 'Pemasukan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pemasukan = Pemasukan::where('id_pemasukan', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        DB::transaction(function() use ($pemasukan) {
            // kurangi saldo
            Rekening::where('id_rekening', $pemasukan->id_rekening)
                   ->decrement('saldo', $pemasukan->jumlah);

            // hapus file bukti jika ada
            if ($pemasukan->bukti_transaksi) {
                Storage::disk('public')->delete($pemasukan->bukti_transaksi);
            }

            $pemasukan->delete();
        });

        return redirect()->route('pemasukan.index')
                         ->with('success', 'Pemasukan berhasil dihapus.');
    }
}
