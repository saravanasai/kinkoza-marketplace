<?php

namespace App\Models;

use Database\Factories\ContactRevealFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'listing_id',
    'user_id',
    'ip_address',
    'user_agent',
    'revealed_at',
])]
class ContactReveal extends Model
{
    /** @use HasFactory<ContactRevealFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'revealed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Listing, $this>
     */
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
