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
        'deskripsi',
        'status',
    ];

    protected $casts = [
        'jumlah'             => 'double',
        'sisa_hutang'        => 'double',
        'tanggal_pinjam'     => 'date',
        'tanggal_jatuh_tempo'=> 'date',
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
}
