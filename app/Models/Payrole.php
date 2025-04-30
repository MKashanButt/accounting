<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payrole extends Model
{
    protected $fillable = [
        'user_id',
        'salary'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
