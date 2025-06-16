<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPengeluaran extends Model
{
    use HasFactory;

    protected $table = 'kategori_pengeluaran';
    protected $primaryKey = 'id_kategori_pengeluaran';
    public $incrementing = true;
    protected $keyType = 'string';

    protected $fillable = ['id_pengguna','nama_kategori','deskripsi','icon'];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class, 'id_kategori');
    }
}
