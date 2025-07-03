<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Rekening;
use App\Models\Anggaran;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard dengan ringkasan, grafik 7 hari, dan grafik anggaran.
     */
    public function index()
    {
        $userId = Auth::user()->id_pengguna;

        // Total saldo
        $totalSaldo = Rekening::where('id_pengguna', $userId)->sum('saldo');

        // Total pemasukan & pengeluaran
        $totalPemasukan   = Pemasukan::where('id_pengguna', $userId)->sum('jumlah');
        $totalPengeluaran = Pengeluaran::where('id_pengguna', $userId)->sum('jumlah');

        // Grafik 7 hari terakhir
        $labels          = [];
        $pemasukanData   = [];
        $pengeluaranData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $labels[]          = $date;
            $pemasukanData[]   = Pemasukan::where('id_pengguna', $userId)
                                           ->whereDate('tanggal', $date)
                                           ->sum('jumlah');
            $pengeluaranData[] = Pengeluaran::where('id_pengguna', $userId)
                                            ->whereDate('tanggal', $date)
                                            ->sum('jumlah');
        }

        // Grafik Anggaran vs Terpakai per kategori
        $anggarans = Anggaran::with('kategori')
            ->where('id_pengguna', $userId)
            ->get();

        $labelsAnggaran = [];
        $batasData      = [];
        $terpakaiData   = [];
        foreach ($anggarans as $ang) {
            $labelsAnggaran[] = $ang->kategori->nama_kategori;
            $batasData[]      = $ang->jumlah_batas;
            $terpakaiData[]   = Pengeluaran::where('id_pengguna', $userId)
                ->where('id_kategori', $ang->id_kategori)
                ->whereBetween('tanggal', [
                    $ang->periode_awal->toDateString(),
                    $ang->periode_akhir->toDateString()
                ])->sum('jumlah');
        }

        return view('dashboard', compact(
            'totalSaldo',
            'totalPemasukan',
            'totalPengeluaran',
            'labels',
            'pemasukanData',
            'pengeluaranData',
            'labelsAnggaran',
            'batasData',
            'terpakaiData'
        ));
    }
}
