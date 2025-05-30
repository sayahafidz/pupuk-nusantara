<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RencanaPemupukanTbm extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rencana_pemupukan_tbm';

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
        'bulan_tanam',
        'bahan_tanam',
        'tahun_pemupukan',
        'semester_pemupukan',
    ];
}
