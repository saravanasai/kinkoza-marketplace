<?php

namespace App\Livewire\Listings;

use App\Actions\Listings\DeleteListingAction;
use App\Actions\Listings\UpdateListingAction;
use App\Enums\Country;
use App\Enums\Currency;
use App\Enums\ListingCategory;
use App\Enums\ListingStatus;
use App\Models\Listing;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Title('Edit listing')]
class Edit extends Component
{
    use WithFileUploads;

    public string $companyName = '';

    public Listing $listing;

    public string $title = '';

    public string $description = '';

    public string $category = ListingCategory::Machinery->value;

    public string $price = '';

    public string $currency = Currency::EUR->value;

    public string $country = Country::FR->value;

    public string $city = '';

    public string $status = ListingStatus::Draft->value;

    public string $publishedAt = '';

    public string $expiresAt = '';

    /**
     * @var array<int, TemporaryUploadedFile>
     */
    public array $images = [];

    /**
     * @var array<int, array{id: int, name: string, url: string}>
     */
    public array $existingImages = [];

    public function mount(Listing $listing): void
    {
        /** @var User $user */
        $user = Auth::user();
        $user->loadMissing('company');

        $this->companyName = $user->company->name;

        abort_unless($user->company_id === $listing->company_id, 404);

        $this->listing = $listing;

        Gate::authorize('update', $this->listing);

        $this->title = $this->listing->title;
        $this->description = $this->listing->description;
        $this->category = $this->listing->category->value;
        $this->price = (string) $this->listing->price;
        $this->currency = $this->listing->currency->value;
        $this->country = $this->listing->country->value;
        $this->city = $this->listing->city;
        $this->status = $this->listing->status->value;
        $this->publishedAt = $this->listing->published_at?->format('Y-m-d\TH:i') ?? '';
        $this->expiresAt = $this->listing->expires_at?->format('Y-m-d\TH:i') ?? '';

        $this->loadExistingImages();
    }

    public function save(UpdateListingAction $updateListingAction): void
    {
        /** @var User $user */
        $user = Auth::user();

        Gate::authorize('update', $this->listing);

        if (count($this->existingImages) + count($this->images) > 4) {
            Flux::toast(variant: 'error', text: __('You may upload up to 4 images.'));

            $this->addError('images', __('You may upload up to 4 images.'));

            return;
        }

        $validated = $this->validate($this->rules());

        $images = $validated['images'] ?? [];

        unset($validated['images']);

        $this->listing = $updateListingAction->handle($user, $this->listing, $validated);

        foreach ($images as $image) {
            $this->listing->addMedia($image)->toMediaCollection('images');
        }

        $this->loadExistingImages();

        Flux::toast(variant: 'success', text: __('Listing updated.'));

        $this->redirect(route('listings.index'), navigate: true);
    }

    public function delete(DeleteListingAction $deleteListingAction): void
    {
        /** @var User $user */
        $user = Auth::user();

        Gate::authorize('delete', $this->listing);

        $deleteListingAction->handle($user, $this->listing);

        Flux::toast(variant: 'success', text: __('Listing deleted.'));

        $this->redirect(route('listings.index'), navigate: true);
    }

    public function removeImage(int $index): void
    {
        unset($this->images[$index]);

        $this->images = array_values($this->images);
    }

    public function removeExistingImage(int $mediaId): void
    {
        Gate::authorize('update', $this->listing);

        $media = $this->listing->getMedia('images')->firstWhere('id', $mediaId);

        abort_if($media === null, 404);

        $media->delete();

        $this->loadExistingImages();
    }

    public function render(): View
    {
        return view('livewire.listings.edit', [
            'categoryOptions' => ListingCategory::cases(),
            'currencyOptions' => Currency::cases(),
            'countryOptions' => Country::cases(),
            'statusOptions' => ListingStatus::cases(),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:5000'],
            'category' => ['required', Rule::enum(ListingCategory::class)],
            'price' => ['required', 'integer', 'min:1'],
            'currency' => ['required', Rule::enum(Currency::class)],
            'country' => ['required', Rule::enum(Country::class)],
            'city' => ['required', 'string', 'max:120'],
            'status' => ['required', Rule::enum(ListingStatus::class)],
            'publishedAt' => ['nullable', 'date'],
            'expiresAt' => ['nullable', 'date'],
            'images' => ['nullable', 'array', 'max:4'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    private function loadExistingImages(): void
    {
        $this->listing = $this->listing->fresh();

        $this->existingImages = $this->listing->getMedia('images')
            ->map(fn ($image): array => [
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
