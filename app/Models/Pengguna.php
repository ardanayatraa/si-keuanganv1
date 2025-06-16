<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'saldo',
        'foto',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int,string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        // jika tidak ada kolom email_verified_at, bisa dihapus
        // 'email_verified_at' => 'datetime',
    ];

    /**
     * Disable default timestamps if tabel tidak pakai created_at/updated_at
     */
    public $timestamps = false;

    // relasi...
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
