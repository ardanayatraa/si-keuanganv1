<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggaran extends Model
{
    use HasFactory;

    protected $table = 'anggaran';
    protected $primaryKey = 'id_anggaran';
    public $incrementing = true;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pengguna',
        'id_kategori',
        'deskripsi',
        'jumlah_batas',
        'periode_awal',
        'periode_akhir',
    ];

    protected $casts = [
        'periode_awal'  => 'date',
        'periode_akhir' => 'date',
    ];


    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'id_kategori');
    }
}
