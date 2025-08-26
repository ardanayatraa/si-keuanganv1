<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utang extends Model
{
    use HasFactory;

    protected $table = 'utang';
    protected $primaryKey = 'id_utang';
    public $incrementing = true;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pengguna',
        'id_rekening',
        'nama',
        'jumlah',
        'sisa_hutang',
        'tanggal_pinjam',
        'tanggal_jatuh_tempo',
        'jangka_waktu_bulan',
        'jumlah_cicilan_per_bulan',
        'deskripsi',
        'status',
    ];

    protected $casts = [
        'jumlah'             => 'double',
        'sisa_hutang'        => 'double',
        'tanggal_pinjam'     => 'date',
        'tanggal_jatuh_tempo'=> 'date',
        'jangka_waktu_bulan' => 'integer',
        'jumlah_cicilan_per_bulan' => 'double',
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    public function rekening()
    {
        return $this->belongsTo(Rekening::class, 'id_rekening');
    }

    public function pembayaranUtang()
    {
        return $this->hasMany(PembayaranUtang::class, 'id_utang');
    }
    public function pembayaran()
    {
        return $this->hasMany(PembayaranUtang::class, 'id_utang');
    }

    /**
     * Generate jadwal cicilan berdasarkan jangka waktu dan jumlah per bulan
     */
    public function getJadwalCicilan()
    {
        if (!$this->jangka_waktu_bulan || !$this->jumlah_cicilan_per_bulan) {
            return collect([]);
        }

        $jadwal = collect();
        $tanggalMulai = $this->tanggal_pinjam;
        $totalTerbayar = $this->pembayaran->sum('jumlah_dibayar');
        $sisaBayar = $totalTerbayar;

        for ($bulan = 1; $bulan <= $this->jangka_waktu_bulan; $bulan++) {
            $tanggalJatuhTempo = $tanggalMulai->copy()->addMonths($bulan);
            $jumlahCicilan = $this->jumlah_cicilan_per_bulan;

            // Hitung status pembayaran untuk cicilan ini
            $status = 'belum_bayar';
            $jumlahTerbayar = 0;

            if ($sisaBayar >= $jumlahCicilan) {
                $status = 'lunas';
                $jumlahTerbayar = $jumlahCicilan;
                $sisaBayar -= $jumlahCicilan;
            } elseif ($sisaBayar > 0) {
                $status = 'sebagian';
                $jumlahTerbayar = $sisaBayar;
                $sisaBayar = 0;
            }

            // Cek apakah sudah lewat jatuh tempo
            if ($status !== 'lunas' && $tanggalJatuhTempo->isPast()) {
                $status = $status === 'sebagian' ? 'terlambat_sebagian' : 'terlambat';
            }

            $jadwal->push((object)[
                'cicilan_ke' => $bulan,
                'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                'jumlah_cicilan' => $jumlahCicilan,
                'jumlah_terbayar' => $jumlahTerbayar,
                'sisa_cicilan' => $jumlahCicilan - $jumlahTerbayar,
                'status' => $status,
                'status_text' => $this->getStatusCicilanText($status),
                'status_class' => $this->getStatusCicilanClass($status)
            ]);
        }

        return $jadwal;
    }

    /**
     * Get text for cicilan status
     */
    private function getStatusCicilanText($status)
    {
        return match($status) {
            'lunas' => 'Lunas',
            'sebagian' => 'Sebagian',
            'terlambat' => 'Terlambat',
            'terlambat_sebagian' => 'Terlambat (Sebagian)',
            default => 'Belum Bayar'
        };
    }

    /**
     * Get CSS class for cicilan status
     */
    private function getStatusCicilanClass($status)
    {
        return match($status) {
            'lunas' => 'bg-green-100 text-green-800',
            'sebagian' => 'bg-yellow-100 text-yellow-800',
            'terlambat' => 'bg-red-100 text-red-800',
            'terlambat_sebagian' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
