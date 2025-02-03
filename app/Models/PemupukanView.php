<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemupukanView extends Model
{
    use HasFactory;

    /**
     * The name of the table associated with the model.
     *
     * @var string
     */
    protected $table = 'pemupukan_view';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = null; // Since it's a view, there may not be a primary key.

    /**
     * Indicate if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false; // Views typically do not have timestamps.

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_pupuk',
        'regional',
        'kebun',
        'afdeling',
        'blok',
        'tahun_tanam',
        'luas_blok',
        'jumlah_pokok',
        'jenis_pupuk',
        'jumlah_pupuk',
        'luas_pemupukan',
        'tgl_pemupukan',
        'semester_pemupukan',
        'rencana_luas_pemupukan',
    ];
}
