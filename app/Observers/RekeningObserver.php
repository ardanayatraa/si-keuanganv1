<?php

namespace App\Observers;

use App\Models\Rekening;

class RekeningObserver
{
    /**
     * Trigger setelah disimpan (create/update)
     */
    public function saved(Rekening $rekening)
    {
        $this->updatePenggunaSaldo($rekening);
    }

    /**
     * Trigger setelah dihapus
     */
    public function deleted(Rekening $rekening)
    {
        $this->updatePenggunaSaldo($rekening);
    }

    /**
     * Logika untuk menghitung ulang saldo total pengguna
     */
    protected function updatePenggunaSaldo(Rekening $rekening)
    {
        $pengguna = $rekening->pengguna;

        if ($pengguna) {
            $totalSaldo = $pengguna->rekening()->sum('saldo');
            $pengguna->update(['saldo' => $totalSaldo]);
        }
    }
}
