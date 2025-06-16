<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    use HasFactory;

    protected $table = 'rekening';
    protected $primaryKey = 'id_rekening';
    public $incrementing = true;
    protected $keyType = 'string';

    protected $fillable = ['id_pengguna','nama_rekening','saldo'];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class, 'id_rekening');
    }

    public function transfersMasuk()
    {
        return $this->hasMany(Transfer::class, 'rekening_tujuan');
    }
}
