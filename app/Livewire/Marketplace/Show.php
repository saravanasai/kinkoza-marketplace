<?php

namespace App\Livewire\Marketplace;

use App\Enums\ListingStatus;
use App\Models\ContactReveal;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Title('View listing')]
#[Layout('layouts.marketplace')]
class Show extends Component
{
    private const CONTACT_REVEAL_MAX_ATTEMPTS = 10;

    private const CONTACT_REVEAL_DECAY_SECONDS = 3600;

    public Listing $listing;

    /**
     * @var array<int, array{id: int, name: string, url: string}>
     */
    public array $images = [];

    public bool $contactRevealed = false;

    public function mount(Listing $listing): void
    {
        abort_unless(
            $listing->status === ListingStatus::Published
                && $listing->published_at?->lessThanOrEqualTo(now()) === true
                && $listing->expires_at?->greaterThan(now()) === true,
            404,
        );

        $this->listing = $listing->loadMissing('company');

        $this->loadImages();
    }

    public function revealContact(): void
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 403);

        $contactReveal = RateLimiter::attempt(
            'contact-reveal:'.$user->id,
            self::CONTACT_REVEAL_MAX_ATTEMPTS,
            fn (): ContactReveal => ContactReveal::create([
                'listing_id' => $this->listing->id,
                'user_id' => $user->id,
                'ip_address' => request()->ip() ?? '0.0.0.0',
                'user_agent' => request()->userAgent(),
                'revealed_at' => now(),
            ]),
            self::CONTACT_REVEAL_DECAY_SECONDS,
        );

        if ($contactReveal === false) {
            $this->addError('contact', __('Too many contact reveals. Try again in :seconds seconds.', [
                'seconds' => RateLimiter::availableIn('contact-reveal:'.$user->id),
            ]));

            return;
        }

        $this->contactRevealed = true;
    }

    public function render(): View
    {
        return view('livewire.marketplace.show', [
            'listing' => $this->listing,
            'images' => $this->images,
        ]);
    }

    private function loadImages(): void
    {
        $this->images = $this->listing->getMedia('images')
            ->map(fn (Media $image): array => [
                'id' => $image->id,
                'name' => $image->name ?: $image->file_name,
                'url' => $this->resolveMediaUrl($image),
            ])
            ->values()
            ->all();
    }

    private function resolveMediaUrl(Media $media): string
    {
        $diskName = $media->disk ?? null;
        $disks = config('filesystems.disks', []);
        $driver = $diskName !== null && isset($disks[$diskName]) ? ($disks[$diskName]['driver'] ?? null) : null;

        if ($driver === 's3') {
            return $media->getTemporaryUrl(now()->addMinutes(15));
        }

        return $media->getUrl();
    }
}
