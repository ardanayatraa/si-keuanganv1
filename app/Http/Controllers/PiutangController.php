<?php

namespace App\Http\Controllers;

use App\Models\Piutang;
use App\Models\Pengeluaran;
use App\Models\Rekening;
use App\Models\KategoriPengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PiutangController extends Controller
{
    /**
     * Daftar piutang milik user login, dengan filter tanggal_pinjam.
     */
    public function index(Request $request)
    {
        $start = $request->input('start_date');
        $end   = $request->input('end_date');

        $query = Piutang::with(['pengguna','rekening'])
            ->where('id_pengguna', Auth::user()->id_pengguna);

        if ($start) {
            $query->whereDate('tanggal_pinjam', '>=', $start);
        }
        if ($end) {
            $query->whereDate('tanggal_pinjam', '<=', $end);
        }

        $items = $query->orderBy('tanggal_pinjam','desc')->get();

        return view('piutang.index', compact('items','start','end'));
    }

    /**
     * Form tambah piutang.
     */
    public function create()
    {
        $rekenings = Rekening::where('id_pengguna',Auth::user()->id_pengguna)->get();
        return view('piutang.create', compact('rekenings'));
    }

    /**
     * Simpan piutang baru: catat pengeluaran + buat piutang.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'                => 'required|string|max:50',
            'id_rekening'         => 'required|exists:rekening,id_rekening',
            'jumlah'              => 'required|numeric|min:0.01',
            'tanggal_pinjam'      => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_pinjam',
            'deskripsi'           => 'nullable|string',
            'bukti_transaksi'     => 'nullable|image|max:2048',
        ]);
        $data['id_pengguna'] = Auth::user()->id_pengguna;

        // Proses upload file bukti transaksi jika ada
        $buktiTransaksi = null;
        if ($request->hasFile('bukti_transaksi')) {
            $buktiTransaksi = $request->file('bukti_transaksi')
                                    ->store('bukti_pengeluaran', 'public');
        }

        DB::transaction(function() use ($data, $buktiTransaksi) {
            $kategori = KategoriPengeluaran::firstOrCreate(
                ['id_pengguna'=>$data['id_pengguna'],'nama_kategori'=>'Piutang'],
                ['deskripsi'=>'Kategori untuk pengeluaran pinjaman (Piutang)','icon'=>'fas fa-handshake']
            );

            // Kurangi saldo rekening
            Rekening::where('id_rekening',$data['id_rekening'])->decrement('saldo',$data['jumlah']);

            // Buat piutang
            $piutang = Piutang::create([
                'nama'                => $data['nama'],
                'id_pengguna'         => $data['id_pengguna'],
                'id_rekening'         => $data['id_rekening'],
                'id_pemasukan'        => null,
                'jumlah'              => $data['jumlah'],
                'sisa_piutang'        => $data['jumlah'],
                'tanggal_pinjam'      => $data['tanggal_pinjam'],
                'tanggal_jatuh_tempo' => $data['tanggal_jatuh_tempo'],
                'deskripsi'           => $data['deskripsi'] ?? null,
                'status'              => 'belum lunas',
            ]);

            // Buat pengeluaran dengan bukti transaksi
            $pengeluaran = Pengeluaran::create([
                'id_pengguna'     => $data['id_pengguna'],
                'id_rekening'     => $data['id_rekening'],
                'jumlah'          => $data['jumlah'],
                'tanggal'         => $data['tanggal_pinjam'],
                'id_kategori'     => $kategori->id_kategori_pengeluaran,
                'deskripsi'       => 'Terima Piutang (ID ' . $piutang->id_piutang . ')',
                'bukti_transaksi' => $buktiTransaksi,
            ]);
        });

        return redirect()->route('piutang.index')
                         ->with('success','Piutang berhasil dicatat dan saldo rekening terpotong.');
    }

    /**
     * Detail piutang.
     */
    public function show($id)
    {
        $piutang = Piutang::with(['pengguna','rekening'])
            ->where('id_piutang',$id)
            ->where('id_pengguna',Auth::user()->id_pengguna)
            ->firstOrFail();
        return view('piutang.show', compact('piutang'));
    }

    /**
     * Form edit piutang.
     */
    public function edit($id)
    {
        $piutang = Piutang::where('id_piutang',$id)
            ->where('id_pengguna',Auth::user()->id_pengguna)
            ->firstOrFail();
        $rekenings = Rekening::where('id_pengguna',Auth::user()->id_pengguna)->get();
        return view('piutang.edit', compact('piutang','rekenings'));
    }

    /**
     * Update piutang, rollback & recreate pengeluaran, reset sisa.
     */
    public function update(Request $request,$id)
    {
        $piutang = Piutang::where('id_piutang',$id)
            ->where('id_pengguna',Auth::user()->id_pengguna)
            ->firstOrFail();

        $data = $request->validate([
            'nama'                => 'required|string|max:50',
            'id_rekening'         => 'required|exists:rekening,id_rekening',
            'jumlah'              => 'required|numeric|min:0.01',
            'tanggal_pinjam'      => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_pinjam',
            'deskripsi'           => 'nullable|string',
            'bukti_transaksi'     => 'nullable|image|max:2048',
        ]);
        $data['id_pengguna'] = Auth::user()->id_pengguna;

        // Proses upload file bukti transaksi jika ada
        $buktiTransaksi = null;
        if ($request->hasFile('bukti_transaksi')) {
            // Hapus file lama jika ada
            $pengeluaranLama = Pengeluaran::where('deskripsi', 'like', '%Terima Piutang (ID ' . $piutang->id_piutang . ')%')
                ->where('id_pengguna', Auth::user()->id_pengguna)
                ->first();

            if ($pengeluaranLama && $pengeluaranLama->bukti_transaksi) {
                Storage::disk('public')->delete($pengeluaranLama->bukti_transaksi);
            }

            $buktiTransaksi = $request->file('bukti_transaksi')
                                    ->store('bukti_pengeluaran', 'public');
        } else {
            // Jika tidak ada file baru, pertahankan file lama
            $pengeluaranLama = Pengeluaran::where('deskripsi', 'like', '%Terima Piutang (ID ' . $piutang->id_piutang . ')%')
                ->where('id_pengguna', Auth::user()->id_pengguna)
                ->first();

            if ($pengeluaranLama) {
                $buktiTransaksi = $pengeluaranLama->bukti_transaksi;
            }
        }

        DB::transaction(function() use ($data, $piutang, $buktiTransaksi) {

            // refund & hapus pengeluaran lama
            Rekening::where('id_rekening',$piutang->id_rekening)->increment('saldo',$piutang->jumlah);
            Pengeluaran::where('deskripsi', 'like', '%Terima Piutang (ID ' . $piutang->id_piutang . ')%')->delete();

            // update piutang
            $piutang->update([
                'nama'                => $data['nama'],
                'id_rekening'         => $data['id_rekening'],
                'jumlah'              => $data['jumlah'],
                'sisa_piutang'        => $data['jumlah'],
                'tanggal_pinjam'      => $data['tanggal_pinjam'],
                'tanggal_jatuh_tempo' => $data['tanggal_jatuh_tempo'],
                'deskripsi'           => $data['deskripsi'] ?? null,
                'status'              => 'belum lunas',
                'id_pemasukan'        => null,
            ]);

            // recreate kategori & pengeluaran
            $kategori = KategoriPengeluaran::firstOrCreate(
                ['id_pengguna'=>$data['id_pengguna'],'nama_kategori'=>'Piutang'],
                ['deskripsi'=>'Kategori untuk pengeluaran pinjaman (Piutang)','icon'=>'fas fa-handshake']
            );

            $pengeluaranBaru = Pengeluaran::create([
                'id_pengguna'     => $data['id_pengguna'],
                'id_rekening'     => $data['id_rekening'],
                'jumlah'          => $data['jumlah'],
                'tanggal'         => $data['tanggal_pinjam'],
                'id_kategori'     => $kategori->id_kategori_pengeluaran,
                'deskripsi'       => 'Terima Piutang (ID ' . $piutang->id_piutang . ')',
                'bukti_transaksi' => $buktiTransaksi,
            ]);

            // Update piutang dengan pengeluaran yang baru dibuat
            $piutang->update([
                'id_pengeluaran' => $pengeluaranBaru->id_pengeluaran,
            ]);

            Rekening::where('id_rekening',$data['id_rekening'])->decrement('saldo',$data['jumlah']);
        });

        return redirect()->route('piutang.index')
                         ->with('success','Piutang berhasil diperbarui dan mutasi rekening disesuaikan.');
    }

    /**
     * Hapus piutang (refund & delete).
     */
    public function destroy($id)
    {
        $piutang = Piutang::where('id_piutang',$id)
            ->where('id_pengguna',Auth::user()->id_pengguna)
            ->firstOrFail();

        DB::transaction(function() use ($piutang) {
            // Hapus file bukti transaksi jika ada
            $pengeluaran = Pengeluaran::where('deskripsi', 'like', '%Terima Piutang (ID ' . $piutang->id_piutang . ')%')
                ->where('id_pengguna', Auth::user()->id_pengguna)
                ->first();

            if ($pengeluaran && $pengeluaran->bukti_transaksi) {
                Storage::disk('public')->delete($pengeluaran->bukti_transaksi);
            }

            // refund saldo & hapus pengeluaran
            Rekening::where('id_rekening',$piutang->id_rekening)->increment('saldo',$piutang->jumlah);
            Pengeluaran::where('deskripsi', 'like', '%Terima Piutang (ID ' . $piutang->id_piutang . ')%')->delete();
            $piutang->delete();
        });

        return redirect()->route('piutang.index')
                         ->with('success','Piutang berhasil dihapus dan saldo rekening dikembalikan.');
    }
}
