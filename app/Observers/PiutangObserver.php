<?php

namespace App\Observers;

use App\Models\Piutang;

class PiutangObserver
{
    /**
     * Handle the Piutang "created" event.
     */
    public function created(Piutang $piutang): void
    {
        // Observer hanya menghitung cicilan, pembayaran melalui sistem pembayaran yang sudah ada
        // Tidak perlu membuat pemasukan secara otomatis
    }

    /**
     * Handle the Piutang "updated" event.
     */
    public function updated(Piutang $piutang): void
    {
        // Observer hanya menghitung cicilan, pembayaran melalui sistem pembayaran yang sudah ada
        // Tidak perlu membuat pemasukan secara otomatis
    }

    /**
     * Handle the Piutang "deleted" event.
     */
    public function deleted(Piutang $piutang): void
    {
        // Observer hanya menghitung cicilan, pembayaran melalui sistem pembayaran yang sudah ada
        // Tidak perlu hapus pemasukan karena tidak dibuat secara otomatis
    }
}
