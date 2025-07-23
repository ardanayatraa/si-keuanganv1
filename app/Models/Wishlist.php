<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wishlist';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_wishlist';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_pengguna',
        'nama_item',
        'kategori',
        'estimasi_harga',
        'tanggal_target',
        'dana_terkumpul',
        'sumber_dana',
        'keterangan',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_target' => 'date',
        'estimasi_harga' => 'double',
        'dana_terkumpul' => 'double',
    ];

    /**
     * Get the user that owns the wishlist item.
     */
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Calculate progress percentage.
     */
    public function getProgressPercentageAttribute()
    {
        if ($this->estimasi_harga <= 0) {
            return 0;
        }

        return min(100, round(($this->dana_terkumpul / $this->estimasi_harga) * 100, 2));
    }

    /**
     * Calculate remaining amount.
     */
    public function getRemainingAmountAttribute()
    {
        return max(0, $this->estimasi_harga - $this->dana_terkumpul);
    }
}
