<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    use HasFactory;

    protected $table        = 'rekening';
    protected $primaryKey   = 'id_rekening';
    public $incrementing    = true;       // sesuaikan jika auto-increment atau manual
    protected $keyType      = 'string';

    protected $fillable     = [
        'id_pengguna',
        'nama_rekening',
        'saldo',
    ];

    /** User pemilik rekening */
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    /** Semua pengeluaran yang pakai rekening ini */
    public function pengeluarans()
    {
        return $this->hasMany(Pengeluaran::class, 'id_rekening');
    }

    /** Semua pemasukan yang pakai rekening ini */
    public function pemasukans()
    {
        return $this->hasMany(Pemasukan::class, 'id_rekening');
    }

    /** Semua pembayaran utang yang pakai rekening ini */
    public function pembayaranUtangs()
    {
        return $this->hasMany(PembayaranUtang::class, 'id_rekening');
    }

    /** Semua pembayaran piutang yang pakai rekening ini */
    public function pembayaranPiutangs()
    {
        return $this->hasMany(PembayaranPiutang::class, 'id_rekening');
    }

    /** Semua transfer keluar (sebagai sumber) */
    public function transfers()
    {
        return $this->hasMany(Transfer::class, 'id_rekening');
    }

    /** Semua transfer masuk (sebagai tujuan) */
    public function transfersMasuk()
    {
        return $this->hasMany(Transfer::class, 'rekening_tujuan');
    }

     public function utangs()
    {
        return $this->hasMany(Utang::class, 'id_rekening');
    }

    /** relasi ke piutang */
    public function piutangs()
    {
        return $this->hasMany(Piutang::class, 'id_rekening');
    }
}
