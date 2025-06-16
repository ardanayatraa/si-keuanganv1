<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran';
    protected $primaryKey = 'id_pengeluaran';
    public $incrementing = true;
    protected $keyType = 'string';

    protected $fillable = ['id_pengguna','jumlah','tanggal','id_kategori','deskripsi','id_rekening'];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'id_kategori');
    }

    public function pembayaranUtang()
    {
        return $this->hasOne(PembayaranUtang::class, 'id_pengeluaran');
    }

    public function rekening()
    {
        return $this->belongsTo(Rekening::class, 'id_rekening');
    }
}
