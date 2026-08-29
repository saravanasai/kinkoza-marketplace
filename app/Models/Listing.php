<?php

namespace App\Models;

use App\Enums\Country;
use App\Enums\Currency;
use App\Enums\ListingCategory;
use App\Enums\ListingStatus;
use Database\Factories\ListingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable([
    'company_id',
    'title',
    'slug',
    'description',
    'category',
    'status',
    'price',
    'currency',
    'country',
    'city',
    'published_at',
    'expires_at',
])]
class Listing extends Model implements HasMedia
{
    /** @use HasFactory<ListingFactory> */
    use HasFactory, InteractsWithMedia;

    protected function casts(): array
    {
        return [
            'category' => ListingCategory::class,
            'status' => ListingStatus::class,
            'currency' => Currency::class,
            'country' => Country::class,
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return HasMany<ContactReveal, $this>
     */
    public function contactReveals(): HasMany
    {
        return $this->hasMany(ContactReveal::class);
    }

    /**
     * @param Builder<Listing> $query
     * @return Builder<Listing>
     */
    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where('status', ListingStatus::Published->value);
    }

    /**
     * @param Builder<Listing> $query
     * @return Builder<Listing>
     */
    #[Scope]
    protected function currentlyOnline(Builder $query): Builder
    {
        return $query->where('status', ListingStatus::Published->value)
            ->where('published_at', '<=', now())
            ->where('expires_at', '>', now());
    }

    /**
     * @param Builder<Listing> $query
     * @return Builder<Listing>
     */
    #[Scope]
    protected function ownedByCompany(Builder $query, Company $company): Builder
    {
        return $query->whereBelongsTo($company);
    }
}
