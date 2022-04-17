<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StripeStateToken extends Model
{
    use HasFactory;

    protected $fillable = ['seller_id', 'token'];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id')->withDefault();
    }
}
