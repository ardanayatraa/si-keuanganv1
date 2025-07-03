<?php

namespace App\Http\Controllers;

use App\Models\Anggaran;
use App\Models\KategoriPengeluaran;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnggaranController extends Controller
{
    public function index(Request $request)
    {
        // Ambil filter periode jika ada
        $start = $request->input('start_date');
        $end   = $request->input('end_date');

        $query = Anggaran::with('kategori')
            ->where('id_pengguna', Auth::user()->id_pengguna);

        if ($start) {
            // periode_awal on or after start
            $query->whereDate('periode_awal', '>=', $start);
        }
        if ($end) {
            // periode_akhir on or before end
            $query->whereDate('periode_akhir', '<=', $end);
        }

        $items = $query
            ->orderBy('periode_awal', 'desc')
            ->get();

        return view('anggaran.index', compact('items', 'start', 'end'));
    }

    public function create()
    {
        $listKategori = KategoriPengeluaran::where('id_pengguna', Auth::user()->id_pengguna)
            ->get();
        return view('anggaran.create', compact('listKategori'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_kategori'   => 'required|exists:kategori_pengeluaran,id_kategori_pengeluaran',
            'deskripsi'     => 'nullable|string',
            'jumlah_batas'  => 'required|numeric|min:0',
            'periode_awal'  => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
        ]);

        $data['id_pengguna'] = Auth::user()->id_pengguna;

        // hitung total pengeluaran di periode & kategori
        $total = Pengeluaran::where('id_pengguna', $data['id_pengguna'])
            ->where('id_kategori', $data['id_kategori'])
            ->whereBetween('tanggal', [$data['periode_awal'], $data['periode_akhir']])
            ->sum('jumlah');

        Anggaran::create($data);

        if ($total > $data['jumlah_batas']) {
            session()->flash('warning',
                "Total pengeluaran Rp " . number_format($total,2,',','.') .
                " sudah melebihi batas anggaran Rp " . number_format($data['jumlah_batas'],2,',','.') .
                " untuk periode tersebut."
            );
        }

        return redirect()->route('anggaran.index')
                         ->with('success','Anggaran berhasil dibuat.');
    }

    public function show($id)
    {
        $anggaran = Anggaran::with('kategori')
            ->where('id_anggaran',$id)
            ->where('id_pengguna',Auth::user()->id_pengguna)
            ->firstOrFail();
        return view('anggaran.show',compact('anggaran'));
    }

    public function edit($id)
    {
        $anggaran = Anggaran::where('id_anggaran',$id)
            ->where('id_pengguna',Auth::user()->id_pengguna)
            ->firstOrFail();
        $listKategori = KategoriPengeluaran::where('id_pengguna',Auth::user()->id_pengguna)->get();
        return view('anggaran.edit',compact('anggaran','listKategori'));
    }

    public function update(Request $request,$id)
    {
        $anggaran = Anggaran::where('id_anggaran',$id)
            ->where('id_pengguna',Auth::user()->id_pengguna)
            ->firstOrFail();

        $data = $request->validate([
            'id_kategori'   => 'required|exists:kategori_pengeluaran,id_kategori_pengeluaran',
            'deskripsi'     => 'nullable|string',
            'jumlah_batas'  => 'required|numeric|min:0',
            'periode_awal'  => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
        ]);

        $userId = Auth::user()->id_pengguna;
        $total = Pengeluaran::where('id_pengguna',$userId)
            ->where('id_kategori',$data['id_kategori'])
            ->whereBetween('tanggal',[$data['periode_awal'],$data['periode_akhir']])
            ->sum('jumlah');

        $anggaran->update($data);

        if ($total > $data['jumlah_batas']) {
            session()->flash('warning',
                "Setelah update, total pengeluaran Rp " . number_format($total,2,',','.') .
                " melebihi batas anggaran Rp " . number_format($data['jumlah_batas'],2,',','.') .
                " untuk periode tersebut."
            );
        }

        return redirect()->route('anggaran.index')
                         ->with('success','Anggaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $anggaran = Anggaran::where('id_anggaran',$id)
            ->where('id_pengguna',Auth::user()->id_pengguna)
            ->firstOrFail();
        $anggaran->delete();

        return redirect()->route('anggaran.index')
                         ->with('success','Anggaran berhasil dihapus.');
    }
}
