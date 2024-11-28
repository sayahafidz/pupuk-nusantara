<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Pemupukan;

class JenisPupuk extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jenis_pupuk';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'kode_pupuk',
        'nama_pupuk',
        'jenis_pupuk',
        'harga',
        'stok',
    ];

    public function pemupukan()
    {
        return $this->hasMany(Pemupukan::class, 'id_pupuk');
    }
}
