<?php
namespace App\Actions\Listings;
use Illuminate\Support\Facades\Gate;
use App\Enums\ListingStatus;
use App\Models\Listing;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateListingAction
{
    use AsAction;

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

        $listing = DB::transaction(function () use ($user, $data): Listing {
            $slug = Str::slug($data['title']) ?: 'listing';

            $uniqueSlug = $slug;
            $suffix = 1;

            while (Listing::query()->where('slug', $uniqueSlug)->exists()) {
                $uniqueSlug = $slug . '-' . $suffix;
                $suffix++;
            }

            $publishedAt = $this->resolvePublishedAt($data);
            $expiresAt = $this->resolveExpiresAt($data, $publishedAt);

            $listing = Listing::query()->create([
                'company_id' => $user->company_id,
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

            return $listing;
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
