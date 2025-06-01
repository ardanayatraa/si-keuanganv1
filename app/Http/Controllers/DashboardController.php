<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Rekening;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard dengan ringkasan dan grafik 7 hari.
     */
    public function index()
    {
        // 1) Total saldo dari semua rekening
        $totalSaldo = Rekening::sum('saldo');

        // 2) Total pemasukan & pengeluaran
        $totalPemasukan   = Pemasukan::sum('jumlah');
        $totalPengeluaran = Pengeluaran::sum('jumlah');

        // 3) Siapkan label & data per hari untuk 7 hari terakhir
        $labels           = [];
        $pemasukanData    = [];
        $pengeluaranData  = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $labels[]          = $date;
            $pemasukanData[]   = Pemasukan::whereDate('tanggal', $date)->sum('jumlah');
            $pengeluaranData[] = Pengeluaran::whereDate('tanggal', $date)->sum('jumlah');
        }

        // 4) Kirim ke view
        return view('dashboard', compact(
            'totalSaldo',
            'totalPemasukan',
            'totalPengeluaran',
            'labels',
            'pemasukanData',
            'pengeluaranData'
        ));
    }
}
