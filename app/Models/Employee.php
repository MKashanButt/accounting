<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'manager_id',
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payrole(): HasOne
    {
        return $this->hasOne(Payrole::class);
    }

    public function commission(): HasMany
    {
        return $this->hasMany(Commission::class);
    }
}
