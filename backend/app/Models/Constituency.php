<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Constituency extends Model
{
    use HasFactory;

    protected $table = 'constituencies';
    protected $fillable = ['corporation_id', 'ac_no', 'name', 'name_kn'];

    public function corporation(): BelongsTo
    {
        return $this->belongsTo(Corporation::class, 'corporation_id');
    }

    public function wards(): HasMany
    {
        return $this->hasMany(Ward::class, 'constituency_id');
    }
}
