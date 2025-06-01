<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranPiutang extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_piutang';
    protected $primaryKey = 'id_pembayaran_piutang';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_piutang',
        'id_pemasukan',
        'id_rekening',          // tambahkan id_rekening
        'jumlah_dibayar',
        'tanggal_pembayaran',
        'metode_pembayaran',
        'deskripsi',
    ];

    protected $casts = [
        'jumlah_dibayar'     => 'double',
        'tanggal_pembayaran' => 'date',
    ];

    public function piutang()
    {
        return $this->belongsTo(Piutang::class, 'id_piutang');
    }

    public function pemasukan()
    {
        return $this->belongsTo(Pemasukan::class, 'id_pemasukan');
    }

    public function rekening()
    {
        return $this->belongsTo(Rekening::class, 'id_rekening');
    }
}
