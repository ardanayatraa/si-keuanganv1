<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengguna extends Model
{
    use HasFactory;

    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['username','email','password'];

    public function kategoriPemasukan()
    {
        return $this->hasMany(KategoriPemasukan::class, 'id_pengguna');
    }

    public function kategoriPengeluaran()
    {
        return $this->hasMany(KategoriPengeluaran::class, 'id_pengguna');
    }

    public function pemasukan()
    {
        return $this->hasMany(Pemasukan::class, 'id_pengguna');
    }

    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class, 'id_pengguna');
    }

    public function anggaran()
    {
        return $this->hasMany(Anggaran::class, 'id_pengguna');
    }

    public function rekening()
    {
        return $this->hasMany(Rekening::class, 'id_pengguna');
    }

    public function utang()
    {
        return $this->hasMany(Utang::class, 'id_pengguna');
    }

    public function piutang()
    {
        return $this->hasMany(Piutang::class, 'id_pengguna');
    }

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'id_pengguna');
    }
}
