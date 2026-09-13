<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Corporation extends Model
{
    use HasFactory;

    protected $table = 'corporations';
    protected $fillable = ['name', 'name_kn', 'bone_merchant_id', 'meat_merchant_id'];

    public function constituencies(): HasMany
    {
        return $this->hasMany(Constituency::class, 'corporation_id');
    }
}
