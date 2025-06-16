<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    use HasFactory;

    protected $table = 'pemasukan';
    protected $primaryKey = 'id_pemasukan';
    public $incrementing = true;
    protected $keyType = 'string';

    protected $fillable = ['id_pengguna','jumlah','tanggal','id_kategori','deskripsi','id_rekening'];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriPemasukan::class, 'id_kategori');
    }

    public function piutang()
    {
        return $this->hasOne(Piutang::class, 'id_pemasukan');
    }

    public function rekening()
    {
        return $this->belongsTo(Rekening::class, 'id_rekening');
    }
}
