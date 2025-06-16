<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $table = 'transfer';
    protected $primaryKey = 'id_transfer';
    public $incrementing = true;
    protected $keyType = 'string';

    protected $fillable = ['id_rekening','rekening_tujuan','jumlah','tanggal'];

    public function rekening()
    {
        return $this->belongsTo(Rekening::class, 'id_rekening');
    }

    public function rekeningTujuan()
    {
        return $this->belongsTo(Rekening::class, 'rekening_tujuan');
    }
}
