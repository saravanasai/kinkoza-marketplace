<?php

namespace App\Models;

use App\Enums\Country;
use App\Enums\Currency;
use App\Enums\ListingCategory;
use App\Enums\ListingStatus;
use Database\Factories\ListingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
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
    use HasFactory, InteractsWithMedia, Searchable, SoftDeletes;

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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')->useDisk('s3');
    }

    /**
     * @return array<string, int|string>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (string) $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category->value,
            'country' => $this->country->value,
            'city' => $this->city,
            'price' => $this->price,
            'status' => $this->status->value,
            'published_at' => $this->published_at?->timestamp ?? 0,
            'expires_at' => $this->expires_at?->timestamp ?? 0,
            'created_at' => $this->created_at->timestamp,
        ];
    }

    public function shouldBeSearchable(): bool
    {
        return $this->status === ListingStatus::Published
            && $this->published_at?->isPast()
            && $this->expires_at?->isFuture();
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
     * @param  Builder<Listing>  $query
     * @return Builder<Listing>
     */
    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where('status', ListingStatus::Published->value);
    }

    /**
     * @param  Builder<Listing>  $query
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
     * @param  Builder<Listing>  $query
     * @return Builder<Listing>
     */
    #[Scope]
    protected function ownedByCompany(Builder $query, Company $company): Builder
    {
        return $query->whereBelongsTo($company);
    }
}
