<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranUtang extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_utang';
    protected $primaryKey = 'id_pembayaran_utang';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id_utang','id_pengeluaran','jumlah_dibayar','tanggal_pembayaran','metode_pembayaran','deskripsi'];

    public function utang()
    {
        return $this->belongsTo(Utang::class, 'id_utang');
    }

    public function pengeluaran()
    {
        return $this->belongsTo(Pengeluaran::class, 'id_pengeluaran');
    }
}
