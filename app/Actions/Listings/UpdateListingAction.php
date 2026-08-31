<?php

namespace App\Actions\Listings;

use App\Enums\ListingStatus;
use App\Models\Listing;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateListingAction
{
    use AsAction;

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(User $user, Listing $listing, array $data, array $images = []): Listing
    {
        Gate::authorize('update', $listing);

        $listing = DB::transaction(function () use ($listing, $data): Listing {
            $slug = Str::slug($data['title']) ?: 'listing';

            $uniqueSlug = $slug;
            $suffix = 1;

            while (Listing::query()->where('slug', $uniqueSlug)->whereKeyNot($listing->getKey())->exists()) {
                $uniqueSlug = $slug.'-'.$suffix;
                $suffix++;
            }

            $publishedAt = $this->resolvePublishedAt($data);
            $expiresAt = $this->resolveExpiresAt($data, $publishedAt);

            $listing->update([
                'title' => $data['title'],
                'slug' => $uniqueSlug,
                'description' => $data['description'],
                'category' => $data['category'],
                'status' => $data['status'],
                'price' => (int) $data['price'],
                'currency' => $data['currency'],
                'country' => $data['country'],
                'city' => $data['city'],
                'published_at' => $publishedAt,
                'expires_at' => $expiresAt,
            ]);

            return $listing->refresh();
        });

         foreach ($images as $image) {
            $listing->addMedia($image)->toMediaCollection('images');
        }

        return $listing;
    }

    private function resolvePublishedAt(array $data): ?Carbon
    {
        if (! empty($data['publishedAt'])) {
            return Carbon::parse($data['publishedAt']);
        }

        if (($data['status'] ?? null) === ListingStatus::Published->value) {
            return now();
        }

        return null;
    }

    private function resolveExpiresAt(array $data, ?Carbon $publishedAt): ?Carbon
    {
        if (! empty($data['expiresAt'])) {
            return Carbon::parse($data['expiresAt']);
        }

        if (($data['status'] ?? null) === ListingStatus::Published->value) {
            return ($publishedAt ?? now())->copy()->addMonth();
        }

        return null;
    }
}
