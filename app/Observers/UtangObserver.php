<?php

namespace App\Observers;

use App\Models\Utang;
use App\Models\Anggaran;
use App\Models\KategoriPengeluaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UtangObserver
{
    /**
     * Handle the Utang "created" event.
     */
    public function created(Utang $utang): void
    {
        Log::info('UtangObserver created called for utang ID: ' . $utang->id_utang, [
            'jangka_waktu_bulan' => $utang->jangka_waktu_bulan,
            'jumlah_cicilan_per_bulan' => $utang->jumlah_cicilan_per_bulan
        ]);

        // Buat anggaran cicilan jika utang memiliki jangka waktu cicilan
        if ($utang->jangka_waktu_bulan && $utang->jumlah_cicilan_per_bulan) {
            try {
                $this->createInstallmentBudgets($utang);
                Log::info('Installment budgets created successfully for utang ID: ' . $utang->id_utang);
            } catch (\Exception $e) {
                Log::error('Error creating installment budgets for utang ID: ' . $utang->id_utang, [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        } else {
            Log::info('No installment budgets created - missing jangka_waktu_bulan or jumlah_cicilan_per_bulan for utang ID: ' . $utang->id_utang);
        }
    }

    /**
     * Handle the Utang "updated" event.
     */
    public function updated(Utang $utang): void
    {
        Log::info('UtangObserver updated called for utang ID: ' . $utang->id_utang, [
            'jangka_waktu_bulan' => $utang->jangka_waktu_bulan,
            'jumlah_cicilan_per_bulan' => $utang->jumlah_cicilan_per_bulan,
            'dirty' => $utang->getDirty()
        ]);

        // Hapus anggaran cicilan lama dan buat yang baru jika ada perubahan cicilan
        if ($utang->isDirty(['jangka_waktu_bulan', 'jumlah_cicilan_per_bulan'])) {
            try {
                $this->deleteInstallmentBudgets($utang);
                Log::info('Old installment budgets deleted for utang ID: ' . $utang->id_utang);

                if ($utang->jangka_waktu_bulan && $utang->jumlah_cicilan_per_bulan) {
                    $this->createInstallmentBudgets($utang);
                    Log::info('New installment budgets created for utang ID: ' . $utang->id_utang);
                }
            } catch (\Exception $e) {
                Log::error('Error updating installment budgets for utang ID: ' . $utang->id_utang, [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
    }

    /**
     * Handle the Utang "deleted" event.
     */
    public function deleted(Utang $utang): void
    {
        // Hapus anggaran cicilan terkait utang
        $this->deleteInstallmentBudgets($utang);
    }

    /**
     * Buat anggaran untuk setiap cicilan utang
     */
    private function createInstallmentBudgets(Utang $utang): void
    {
        Log::info('Creating installment budgets for utang ID: ' . $utang->id_utang, [
            'utang_nama' => $utang->nama,
            'jangka_waktu_bulan' => $utang->jangka_waktu_bulan,
            'jumlah_cicilan_per_bulan' => $utang->jumlah_cicilan_per_bulan,
            'tanggal_pinjam' => $utang->tanggal_pinjam
        ]);

        // Pastikan kategori "Cicilan Utang" ada
        $kategoriCicilan = KategoriPengeluaran::firstOrCreate(
            ['id_pengguna' => $utang->id_pengguna, 'nama_kategori' => 'Cicilan Utang'],
            ['deskripsi' => 'Kategori untuk cicilan utang', 'icon' => 'fas fa-money-bill-wave']
        );

        Log::info('Kategori Cicilan Utang: ' . $kategoriCicilan->id_kategori_pengeluaran);

        $tanggalMulai = $utang->tanggal_pinjam;

        for ($bulan = 1; $bulan <= $utang->jangka_waktu_bulan; $bulan++) {
            $tanggalJatuhTempo = $tanggalMulai->copy()->addMonths($bulan);
            $periodeAwal = $tanggalJatuhTempo->copy()->startOfMonth();
            $periodeAkhir = $tanggalJatuhTempo->copy()->endOfMonth();

            Log::info('Creating budget for month ' . $bulan, [
                'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                'periode_awal' => $periodeAwal,
                'periode_akhir' => $periodeAkhir
            ]);

            try {
                $anggaran = Anggaran::create([
                    'id_pengguna' => $utang->id_pengguna,
                    'id_kategori' => $kategoriCicilan->id_kategori_pengeluaran,
                    'deskripsi' => 'Cicilan utang ' . $utang->nama . ' (Cicilan ke-' . $bulan . ')',
                    'jumlah_batas' => $utang->jumlah_cicilan_per_bulan,
                    'periode_awal' => $periodeAwal,
                    'periode_akhir' => $periodeAkhir,
                ]);
                Log::info('Budget created successfully: ' . $anggaran->id_anggaran);
            } catch (\Exception $e) {
                Log::error('Error creating budget for month ' . $bulan . ' for utang ID: ' . $utang->id_utang, [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e; // Re-throw to handle in the calling method
            }
        }
    }

    /**
     * Hapus anggaran cicilan terkait utang
     */
    private function deleteInstallmentBudgets(Utang $utang): void
    {
        Anggaran::where('id_pengguna', $utang->id_pengguna)
            ->where('deskripsi', 'like', '%Cicilan utang ' . $utang->nama . '%')
            ->delete();
    }
}
