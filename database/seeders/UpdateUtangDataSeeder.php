<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Utang;
use App\Models\PembayaranUtang;

class UpdateUtangDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update semua utang yang sudah ada
        $utangs = Utang::all();
        
        foreach ($utangs as $utang) {
            // Hitung total pembayaran yang sudah dilakukan
            $totalPembayaran = PembayaranUtang::where('id_utang', $utang->id_utang)
                ->sum('jumlah_dibayar');
            
            // Hitung sisa hutang
            $sisaHutang = $utang->jumlah - $totalPembayaran;
            
            // Tentukan status
            $status = $sisaHutang <= 0 ? 'lunas' : 'aktif';
            
            // Update utang
            $utang->update([
                'sisa_hutang' => max(0, $sisaHutang), // Pastikan tidak negatif
                'status' => $status
            ]);
        }
        
        $this->command->info('Data utang berhasil diupdate dengan sisa_hutang dan status.');
    }
}
