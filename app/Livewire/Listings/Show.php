<?php

namespace App\Livewire\Listings;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Title('View listing')]
class Show extends Component
{
    public Listing $listing;

    public string $companyName = '';

    public int $contactRevealsCount = 0;

    /**
     * @var array<int, array{id: int, name: string, url: string}>
     */
    public array $images = [];

    public function mount(Listing $listing): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->listing = $listing;

        Gate::authorize('view', $this->listing);

        $this->companyName = $listing->company->name;

        $this->contactRevealsCount = $this->listing->contactReveals()->count();

        $this->loadImages();
    }

    public function render(): View
    {
        return view('livewire.listings.show');
    }

    private function loadImages(): void
    {
        $this->images = $this->listing->getMedia('images')
            ->map(fn(Media $image): array => [
                'id' => $image->id,
                'name' => $image->name ?: $image->file_name,
                'url' => $this->resolveMediaUrl($image),
            ])
            ->values()
            ->all();
    }

    private function resolveMediaUrl(Media $media): string
    {
        return $media->getTemporaryUrl(now()->addMinutes(15));
    }
}
