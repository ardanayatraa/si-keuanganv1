<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aset extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'aset';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_aset';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_pengguna',
        'nama_aset',
        'jenis_aset',
        'nilai_aset',
        'tanggal_perolehan',
        'keterangan',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_perolehan' => 'date',
        'nilai_aset' => 'double',
    ];

    /**
     * Get the user that owns the asset.
     */
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Get the history records for the asset.
     */
    public function history()
    {
        return $this->hasMany(AsetHistory::class, 'id_aset', 'id_aset');
    }
}
