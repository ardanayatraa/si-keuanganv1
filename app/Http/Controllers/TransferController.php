<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransferController extends Controller
{
    /**
     * Tampilkan daftar transfer yang dibuat user login.
     */
    public function index()
    {
        $items = Transfer::with(['rekening','rekeningTujuan'])
            ->whereHas('rekening', fn($q) =>
                $q->where('id_pengguna', Auth::user()->id_pengguna)
            )
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('transfer.index', compact('items'));
    }

    /**
     * Form buat transfer baru. Rekening hanya milik user.
     */
    public function create()
    {
        $rekenings = Rekening::where('id_pengguna', Auth::user()->id_pengguna)
                              ->get();

        return view('transfer.create', compact('rekenings'));
    }

    /**
     * Simpan transfer baru dan update saldo.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_rekening'      => 'required|exists:rekening,id_rekening',
            'rekening_tujuan'  => 'required|exists:rekening,id_rekening|different:id_rekening',
            'jumlah'           => 'required|numeric|min:0.01',
            'tanggal'          => 'required|date',
        ]);

        // pastikan rekening asal milik user
        $asal = Rekening::where('id_rekening', $data['id_rekening'])
                        ->where('id_pengguna', Auth::user()->id_pengguna)
                        ->firstOrFail();

        if ($asal->saldo < $data['jumlah']) {
            return back()
                ->withErrors(['jumlah' => 'Saldo rekening asal tidak mencukupi.'])
                ->withInput();
        }

        DB::transaction(function() use ($data) {
            // debit rekening asal
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->decrement('saldo', $data['jumlah']);

            // kredit rekening tujuan
            Rekening::where('id_rekening', $data['rekening_tujuan'])
                   ->increment('saldo', $data['jumlah']);

            // simpan record transfer
            Transfer::create($data);
        });

        return redirect()->route('transfer.index')
                         ->with('success', 'Transfer berhasil dibuat dan saldo terupdate.');
    }

    /**
     * Detail satu transfer (pastikan milik user).
     */
    public function show($id)
    {
        $transfer = Transfer::with(['rekening','rekeningTujuan'])
            ->where('id_transfer', $id)
            ->whereHas('rekening', fn($q) =>
                $q->where('id_pengguna', Auth::user()->id_pengguna)
            )
            ->firstOrFail();

        return view('transfer.show', compact('transfer'));
    }

    /**
     * Form edit transfer (pastikan milik user).
     */
    public function edit($id)
    {
        $transfer = Transfer::where('id_transfer', $id)
            ->whereHas('rekening', fn($q) =>
                $q->where('id_pengguna', Auth::user()->id_pengguna)
            )
            ->firstOrFail();

        $rekenings = Rekening::where('id_pengguna', Auth::user()->id_pengguna)
                              ->get();

        return view('transfer.edit', compact('transfer','rekenings'));
    }

    /**
     * Update transfer dan sesuaikan saldo lama & baru.
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'id_rekening'      => 'required|exists:rekening,id_rekening',
            'rekening_tujuan'  => 'required|exists:rekening,id_rekening|different:id_rekening',
            'jumlah'           => 'required|numeric|min:0.01',
            'tanggal'          => 'required|date',
        ]);

        $transfer = Transfer::where('id_transfer', $id)
            ->whereHas('rekening', fn($q) =>
                $q->where('id_pengguna', Auth::user()->id_pengguna)
            )
            ->firstOrFail();

        DB::transaction(function() use ($data, $transfer) {
            // rollback saldo lama
            Rekening::where('id_rekening', $transfer->id_rekening)
                   ->increment('saldo', $transfer->jumlah);
            Rekening::where('id_rekening', $transfer->rekening_tujuan)
                   ->decrement('saldo', $transfer->jumlah);

            // cek kecukupan saldo rekening baru
            $newAsal = Rekening::where('id_rekening', $data['id_rekening'])
                               ->where('id_pengguna', Auth::user()->id_pengguna)
                               ->firstOrFail();
            if ($newAsal->saldo < $data['jumlah']) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Saldo rekening asal tidak mencukupi.'
                ]);
            }

            // terapkan transfer baru
            $newAsal->decrement('saldo', $data['jumlah']);
            Rekening::where('id_rekening', $data['rekening_tujuan'])
                   ->increment('saldo', $data['jumlah']);

            // update record
            $transfer->update($data);
        });

        return redirect()->route('transfer.index')
                         ->with('success','Transfer berhasil diperbarui dan saldo terupdate.');
    }

    /**
     * Hapus transfer dan rollback saldo.
     */
    public function destroy($id)
    {
        $transfer = Transfer::where('id_transfer', $id)
            ->whereHas('rekening', fn($q) =>
                $q->where('id_pengguna', Auth::user()->id_pengguna)
            )
            ->firstOrFail();

        DB::transaction(function() use ($transfer) {
            // rollback efek transfer
            Rekening::where('id_rekening', $transfer->id_rekening)
                   ->increment('saldo', $transfer->jumlah);
            Rekening::where('id_rekening', $transfer->rekening_tujuan)
                   ->decrement('saldo', $transfer->jumlah);

            // hapus record
            $transfer->delete();
        });

        return redirect()->route('transfer.index')
                         ->with('success','Transfer berhasil dihapus dan saldo direstorasi.');
    }
}
