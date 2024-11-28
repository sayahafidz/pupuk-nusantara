<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Pemupukan;

class MasterData extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'master_data';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'kondisi',
        'status_umur',
        'kode_kebun',
        'nama_kebun',
        'kkl_kebun',
        'afdeling',
        'tahun_tanam',
        'no_blok',
        'luas',
        'jlh_pokok',
        'pkk_ha',
    ];

    public function pemupukan()
    {
        return $this->hasMany(Pemupukan::class, 'id_master_data');
    }
}
