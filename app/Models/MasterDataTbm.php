<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterDataTbm extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'master_data_tbm';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'regional',
        'kode_kebun',
        'nama_kebun',
        'afdeling',
        'blok',
        'tahun_tanam',
        'bulan_tanam',
        'luas_ha',
        'jumlah_pohon',
        'bahan_tanam'
    ];
}
