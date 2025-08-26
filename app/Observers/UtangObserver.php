<?php

namespace App\Observers;

use App\Models\Utang;

class UtangObserver
{
    /**
     * Handle the Utang "created" event.
     */
    public function created(Utang $utang): void
    {
        // Observer hanya menghitung cicilan, pembayaran melalui sistem pembayaran yang sudah ada
        // Tidak perlu membuat pengeluaran secara otomatis
    }

    /**
     * Handle the Utang "updated" event.
     */
    public function updated(Utang $utang): void
    {
        // Observer hanya menghitung cicilan, pembayaran melalui sistem pembayaran yang sudah ada
        // Tidak perlu membuat pengeluaran secara otomatis
    }

    /**
     * Handle the Utang "deleted" event.
     */
    public function deleted(Utang $utang): void
    {
        // Observer hanya menghitung cicilan, pembayaran melalui sistem pembayaran yang sudah ada
        // Tidak perlu hapus pengeluaran karena tidak dibuat secara otomatis
    }
}
