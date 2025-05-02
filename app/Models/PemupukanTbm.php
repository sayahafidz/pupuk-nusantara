<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PemupukanTbm extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pemupukan_tbm';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_pupuk',
        'id_master_data_tbm',
        'regional',
        'kebun',
        'afdeling',
        'blok',
        'tahun_tanam',
        'luas_blok',
        'jumlah_pokok',
        'bulan_tanam',
        'bahan_tanam',
        'jenis_pupuk',
        'jumlah_pupuk',
        'luas_pemupukan',
        'tgl_pemupukan',
    ];

    
}

