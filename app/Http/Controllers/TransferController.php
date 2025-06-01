<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    /**
     * Tampilkan daftar transfer.
     */
    public function index()
    {
        $items = Transfer::with('rekening','rekeningTujuan')->get();
        return view('transfer.index', compact('items'));
    }

    /**
     * Tampilkan form buat transfer baru.
     */
    public function create()
    {
        $rekenings = Rekening::all();
        return view('transfer.create', compact('rekenings'));
    }

    /**
     * Simpan transfer baru dan update saldo.
     */
    public function store(Request $request)
    {
        // validasi input
        $data = $request->validate([
            'id_rekening'      => 'required|exists:rekening,id_rekening',
            'rekening_tujuan'  => 'required|exists:rekening,id_rekening|different:id_rekening',
            'jumlah'           => 'required|numeric|min:0.01',
            'tanggal'          => 'required|date',
        ]);

        // cek kecukupan saldo sebelum simpan
        $asal = Rekening::find($data['id_rekening']);
        if ($asal->saldo < $data['jumlah']) {
            return redirect()
                ->back()
                ->withErrors(['jumlah' => 'Saldo rekening asal tidak mencukupi.'])
                ->withInput();
        }

        DB::transaction(function() use ($data) {
            // debit rekening asal
            $asal = Rekening::findOrFail($data['id_rekening']);
            $asal->decrement('saldo', $data['jumlah']);

            // kredit rekening tujuan
            $tujuan = Rekening::findOrFail($data['rekening_tujuan']);
            $tujuan->increment('saldo', $data['jumlah']);

            // simpan record transfer
            Transfer::create($data);
        });

        return redirect()->route('transfer.index')
                         ->with('success','Transfer berhasil dibuat dan saldo terupdate.');
    }

    /**
     * Tampilkan detail satu transfer.
     */
    public function show(Transfer $transfer)
    {
        return view('transfer.show', compact('transfer'));
    }

    /**
     * Tampilkan form edit transfer.
     */
    public function edit(Transfer $transfer)
    {
        $rekenings = Rekening::all();
        return view('transfer.edit', compact('transfer','rekenings'));
    }

    /**
     * Update transfer dan sesuaikan saldo lama & baru.
     */
    public function update(Request $request, Transfer $transfer)
    {
        $data = $request->validate([
            'id_rekening'      => 'required|exists:rekening,id_rekening',
            'rekening_tujuan'  => 'required|exists:rekening,id_rekening|different:id_rekening',
            'jumlah'           => 'required|numeric|min:0.01',
            'tanggal'          => 'required|date',
        ]);

        DB::transaction(function() use ($data, $transfer) {
            // rollback saldo transfer lama
            $oldAsal   = Rekening::findOrFail($transfer->id_rekening);
            $oldTujuan = Rekening::findOrFail($transfer->rekening_tujuan);
            $oldAsal->increment('saldo', $transfer->jumlah);
            $oldTujuan->decrement('saldo', $transfer->jumlah);

            // cek kecukupan saldo rekening baru
            $newAsal = Rekening::findOrFail($data['id_rekening']);
            if ($newAsal->saldo < $data['jumlah']) {
                // manual throw agar ditangani di luar DB transaction
                throw new \Illuminate\Validation\ValidationException(
                    validator: \Validator::make([], []),
                    response: redirect()->back()
                        ->withErrors(['jumlah' => 'Saldo rekening asal tidak mencukupi.'])
                        ->withInput()
                );
            }

            // terapkan transfer baru
            $newAsal->decrement('saldo', $data['jumlah']);
            $newTujuan = Rekening::findOrFail($data['rekening_tujuan']);
            $newTujuan->increment('saldo', $data['jumlah']);

            // update record
            $transfer->update($data);
        });

        return redirect()->route('transfer.index')
                         ->with('success','Transfer berhasil diperbarui dan saldo terupdate.');
    }

    /**
     * Hapus transfer dan rollback saldo.
     */
    public function destroy(Transfer $transfer)
    {
        DB::transaction(function() use ($transfer) {
            // rollback efek transfer
            $asal   = Rekening::findOrFail($transfer->id_rekening);
            $tujuan = Rekening::findOrFail($transfer->rekening_tujuan);
            $asal->increment('saldo', $transfer->jumlah);
            $tujuan->decrement('saldo', $transfer->jumlah);

            // hapus record
            $transfer->delete();
        });

        return redirect()->route('transfer.index')
                         ->with('success','Transfer berhasil dihapus dan saldo direstorasi.');
    }
}
