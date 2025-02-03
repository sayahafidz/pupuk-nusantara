<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Whatsapp extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'whatsapp';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone',
        'status',
        'user_id',
    ];

    /**
     * Get the user that owns the Whatsapp
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user that owns the Whatsapp
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get the user that owns the Whatsapp
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}
