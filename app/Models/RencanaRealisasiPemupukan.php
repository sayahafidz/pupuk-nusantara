<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RencanaRealisasiPemupukan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rencana_realisasi_pemupukan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'regional',
        'kebun',
        'afdeling',
        'rencana_plant',
        'realisasi_plant',
        'jenis_pupuk',
        'tahun_tanam',
        'rencana_semester_1',
        'realisasi_semester_1',
        'rencana_semester_2',
        'realisasi_semester_2',
        'rencana_total',
        'realisasi_total',
    ];
}
