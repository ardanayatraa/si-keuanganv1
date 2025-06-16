<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Piutang extends Model
{
    use HasFactory;

    protected $table = 'piutang';
    protected $primaryKey = 'id_piutang';
    protected $keyType = 'string';

    protected $fillable = [
        'id_pengguna',
        'id_rekening',
        'id_pemasukan',
        'jumlah',
        'sisa_piutang',
        'tanggal_pinjam',
        'tanggal_jatuh_tempo',
        'deskripsi',
        'status',
    ];

    protected $casts = [
        'jumlah'             => 'double',
        'sisa_piutang'       => 'double',
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

    public function pemasukan()
    {
        return $this->belongsTo(Pemasukan::class, 'id_pemasukan');
    }

    public function pembayaranPiutang()
    {
        return $this->hasMany(PembayaranPiutang::class, 'id_piutang');
    }
}
