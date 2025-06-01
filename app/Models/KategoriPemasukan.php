<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPemasukan extends Model
{
    use HasFactory;

    protected $table = 'kategori_pemasukan';
    protected $primaryKey = 'id_kategori_pemasukan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id_pengguna','nama_kategori','deskripsi','icon'];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    public function pemasukan()
    {
        return $this->hasMany(Pemasukan::class, 'id_kategori');
    }
}
