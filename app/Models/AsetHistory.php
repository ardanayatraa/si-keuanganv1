<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsetHistory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'aset_history';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_aset',
        'nilai_lama',
        'nilai_baru',
        'tanggal_perubahan',
        'keterangan'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_perubahan' => 'date',
        'nilai_lama' => 'double',
        'nilai_baru' => 'double',
    ];

    /**
     * Get the asset that owns the history record.
     */
    public function aset()
    {
        return $this->belongsTo(Aset::class, 'id_aset', 'id_aset');
    }
}
