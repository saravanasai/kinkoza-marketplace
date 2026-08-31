<?php
namespace App\Actions\Listings;

use App\Enums\ListingStatus;
use App\Models\Listing;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateListingAction
{
    use AsAction;

    private const MAX_SLUG_ATTEMPTS = 5;

    /**
     * @param  array<int, mixed>  $images
     */
    public function handle(User $user, array $data, array $images = []): Listing
    {
        Gate::authorize('create', [Listing::class, $user->company]);

        if (count($images) > 4) {
            throw ValidationException::withMessages([
                'images' => __('You may upload up to 4 images.'),
            ]);
        }

        $listing = $this->persistListing($user, $data);

        foreach ($images as $image) {
            $listing->addMedia($image)->toMediaCollection('images');
        }

        return $listing;
    }

    private function persistListing(User $user, array $data): Listing
    {
        for ($attempt = 0; $attempt < self::MAX_SLUG_ATTEMPTS; $attempt++) {
            try {
                return DB::transaction(function () use ($user, $data): Listing {
                    $publishedAt = $this->resolvePublishedAt($data);
                    $expiresAt = $this->resolveExpiresAt($data, $publishedAt);
                    $slug = $this->resolveUniqueSlug($data['title']);

                    return Listing::query()->create([
                        'company_id' => $user->company_id,
                        'title' => $data['title'],
                        'slug' => $slug,
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
                });
            } catch (QueryException $exception) {
                if (! $this->isDuplicateSlugException($exception) || $attempt === self::MAX_SLUG_ATTEMPTS - 1) {
                    throw $exception;
                }
            }
        }

        throw new \RuntimeException('Unable to generate a unique listing slug.');
    }

    private function resolveUniqueSlug(string $title): string
    {
        $baseSlug = Str::slug($title) ?: 'listing';
        $uniqueSlug = $baseSlug;
        $suffix = 1;

        while (Listing::query()->where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $uniqueSlug;
    }

    private function isDuplicateSlugException(QueryException $exception): bool
    {
        return Str::contains($exception->getMessage(), ['UNIQUE constraint failed', 'Duplicate entry', 'listings.slug']);
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
