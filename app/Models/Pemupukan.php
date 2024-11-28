<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pemupukan extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pemupukan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_pupuk',
        'id_master_data',
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
    ];

    public function masterData()
    {
        return $this->belongsTo(MasterData::class, 'id_master_data');
    }


}
