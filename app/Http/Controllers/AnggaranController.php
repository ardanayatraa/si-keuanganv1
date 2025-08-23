<?php

namespace App\Http\Controllers;

use App\Models\Anggaran;
use App\Models\KategoriPengeluaran;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class AnggaranController extends Controller
{
    public function index(Request $request)
    {
        // Ambil filter tahun dan bulan
        $tahun = $request->input('tahun', date('Y'));
        $bulan = $request->input('bulan');

        $query = Anggaran::with('kategori')
            ->where('id_pengguna', Auth::user()->id_pengguna);

        // Filter berdasarkan tahun
        if ($tahun) {
            $query->where(function($q) use ($tahun) {
                $q->whereYear('periode_awal', $tahun)
                  ->orWhereYear('periode_akhir', $tahun);
            });
        }
        
        // Filter berdasarkan bulan jika dipilih
        if ($bulan) {
            $query->where(function($q) use ($bulan, $tahun) {
                $q->where(function($subQ) use ($bulan) {
                    $subQ->whereMonth('periode_awal', $bulan)
                         ->orWhereMonth('periode_akhir', $bulan);
                });
                // Pastikan tahun juga sesuai jika bulan dipilih
                if ($tahun) {
                    $q->where(function($subQ) use ($tahun) {
                        $subQ->whereYear('periode_awal', $tahun)
                             ->orWhereYear('periode_akhir', $tahun);
                    });
                }
            });
        }

        $items = $query
            ->orderBy('periode_awal', 'desc')
            ->get();

        // List tahun untuk dropdown (5 tahun ke belakang dan ke depan)
        $tahunList = collect(range(date('Y') - 5, date('Y') + 5));

        return view('anggaran.index', compact('items', 'tahun', 'bulan', 'tahunList'));
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

    public function laporan(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $bulan = $request->input('bulan');
        
        // Ambil data anggaran berdasarkan filter tahun dan bulan
        $query = Anggaran::with('kategori', 'pengguna')
            ->where('id_pengguna', Auth::user()->id_pengguna);

        // Filter berdasarkan tahun
        if ($tahun) {
            $query->where(function($q) use ($tahun) {
                $q->whereYear('periode_awal', $tahun)
                  ->orWhereYear('periode_akhir', $tahun);
            });
        }
        
        // Filter berdasarkan bulan jika dipilih
        if ($bulan) {
            $query->where(function($q) use ($bulan, $tahun) {
                $q->where(function($subQ) use ($bulan) {
                    $subQ->whereMonth('periode_awal', $bulan)
                         ->orWhereMonth('periode_akhir', $bulan);
                });
                // Pastikan tahun juga sesuai jika bulan dipilih
                if ($tahun) {
                    $q->where(function($subQ) use ($tahun) {
                        $subQ->whereYear('periode_awal', $tahun)
                             ->orWhereYear('periode_akhir', $tahun);
                    });
                }
            });
        }

        $anggarans = $query->orderBy('periode_awal', 'desc')->get();

        // Hitung total realisasi untuk setiap anggaran
        $laporan = $anggarans->map(function ($anggaran) {
            $totalRealisasi = Pengeluaran::where('id_pengguna', $anggaran->id_pengguna)
                ->where('id_kategori', $anggaran->id_kategori)
                ->whereBetween('tanggal', [
                    $anggaran->periode_awal,
                    $anggaran->periode_akhir
                ])
                ->sum('jumlah');

            $anggaran->total_realisasi = $totalRealisasi;
            $anggaran->sisa_anggaran = $anggaran->jumlah_batas - $totalRealisasi;
            $anggaran->persentase_terpakai = $anggaran->jumlah_batas > 0 
                ? ($totalRealisasi / $anggaran->jumlah_batas) * 100 
                : 0;
            
            return $anggaran;
        });

        // Hitung total keseluruhan
        $totalAnggaran = $laporan->sum('jumlah_batas');
        $totalRealisasi = $laporan->sum('total_realisasi');
        $totalSisa = $totalAnggaran - $totalRealisasi;

        // List tahun untuk dropdown (5 tahun ke belakang dan ke depan)
        $tahunList = collect(range(date('Y') - 5, date('Y') + 5));
        
        return view('anggaran.laporan', compact(
            'laporan', 
            'tahun', 
            'bulan', 
            'totalAnggaran',
            'totalRealisasi', 
            'totalSisa',
            'tahunList'
        ));
    }

    public function exportPdf(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $bulan = $request->input('bulan');
        
        // Ambil data anggaran berdasarkan filter tahun dan bulan (sama seperti method laporan)
        $query = Anggaran::with('kategori', 'pengguna')
            ->where('id_pengguna', Auth::user()->id_pengguna);

        // Filter berdasarkan tahun
        if ($tahun) {
            $query->where(function($q) use ($tahun) {
                $q->whereYear('periode_awal', $tahun)
                  ->orWhereYear('periode_akhir', $tahun);
            });
        }
        
        // Filter berdasarkan bulan jika dipilih
        if ($bulan) {
            $query->where(function($q) use ($bulan, $tahun) {
                $q->where(function($subQ) use ($bulan) {
                    $subQ->whereMonth('periode_awal', $bulan)
                         ->orWhereMonth('periode_akhir', $bulan);
                });
                // Pastikan tahun juga sesuai jika bulan dipilih
                if ($tahun) {
                    $q->where(function($subQ) use ($tahun) {
                        $subQ->whereYear('periode_awal', $tahun)
                             ->orWhereYear('periode_akhir', $tahun);
                    });
                }
            });
        }

        $anggarans = $query->orderBy('periode_awal', 'desc')->get();

        // Hitung total realisasi untuk setiap anggaran
        $laporan = $anggarans->map(function ($anggaran) {
            $totalRealisasi = Pengeluaran::where('id_pengguna', $anggaran->id_pengguna)
                ->where('id_kategori', $anggaran->id_kategori)
                ->whereBetween('tanggal', [
                    $anggaran->periode_awal,
                    $anggaran->periode_akhir
                ])
                ->sum('jumlah');

            $anggaran->total_realisasi = $totalRealisasi;
            $anggaran->sisa_anggaran = $anggaran->jumlah_batas - $totalRealisasi;
            $anggaran->persentase_terpakai = $anggaran->jumlah_batas > 0 
                ? ($totalRealisasi / $anggaran->jumlah_batas) * 100 
                : 0;
            
            return $anggaran;
        });

        // Hitung total keseluruhan
        $totalAnggaran = $laporan->sum('jumlah_batas');
        $totalRealisasi = $laporan->sum('total_realisasi');
        $totalSisa = $totalAnggaran - $totalRealisasi;

        // Generate nama file
        $periode = $bulan ? 
            \Carbon\Carbon::createFromFormat('m', $bulan)->format('F') . '_' . $tahun : 
            'Tahun_' . $tahun;
        
        $filename = 'Laporan_Anggaran_' . $periode . '_' . date('Y-m-d') . '.pdf';

        // Generate PDF
        $pdf = Pdf::loadView('anggaran.print-laporan', [
            'laporan' => $laporan,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'totalAnggaran' => $totalAnggaran,
            'totalRealisasi' => $totalRealisasi,
            'totalSisa' => $totalSisa,
            'generated_at' => now()->format('d F Y H:i')
        ])->setPaper('a4', 'portrait');

        return $pdf->stream($filename);
    }
}
