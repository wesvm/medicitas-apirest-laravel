<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Token extends Model
{
    protected $fillable = [
        'token',
        'token_type',
        'revoked',
        'expired',
        'expires_at',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'revoked' => 'boolean',
            'expired' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }
}
