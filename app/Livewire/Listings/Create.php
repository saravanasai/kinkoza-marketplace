<?php

namespace App\Livewire\Listings;

use App\Actions\Listings\CreateListingAction;
use App\Enums\Country;
use App\Enums\Currency;
use App\Enums\ListingCategory;
use App\Enums\ListingStatus;
use App\Models\Listing;
use App\Models\User;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Title('Create listing')]
class Create extends Component
{
    use WithFileUploads;

    public string $companyName = '';

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

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $user->loadMissing('company');

        $this->companyName = $user->company->name;
    }

    public function save(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $user->loadMissing('company');

        Gate::authorize('create', [Listing::class, $user->company]);

        if (count($this->images) > 4) {
            Flux::toast(variant: 'error', text: __('You may upload up to 4 images.'));

            $this->addError('images', __('You may upload up to 4 images.'));

            return;
        }

        try {
            $validated = $this->validate($this->rules());
        } catch (ValidationException $exception) {
            if ($this->hasImageValidationError($exception)) {
                Flux::toast(variant: 'error', text: __('Please fix the image upload errors.'));
            }

            throw $exception;
        }

        $images = $validated['images'] ?? [];

        unset($validated['images']);

        CreateListingAction::run($user, $validated, $images);

        Flux::toast(variant: 'success', text: __('Listing created.'));

        $this->redirect(route('listings.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.listings.create', [
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
            'expiresAt' => [
                'nullable',
                'date',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (empty($value)) {
                        return;
                    }

                    $publishedAt = $this->publishedAt !== ''
                        ? Carbon::parse($this->publishedAt)
                        : ($this->status === ListingStatus::Published->value ? now() : null);

                    if ($publishedAt === null) {
                        return;
                    }

                    if (Carbon::parse($value)->lessThanOrEqualTo($publishedAt)) {
                        $fail(__('The expiry date must be after the published date.'));
                    }
                },
            ],
            'images' => ['nullable', 'array', 'max:4'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function removeImage(int $index): void
    {
        unset($this->images[$index]);

        $this->images = array_values($this->images);
    }

    private function hasImageValidationError(ValidationException $exception): bool
    {
        foreach (array_keys($exception->errors()) as $key) {
            if (str_starts_with($key, 'images')) {
                return true;
            }
        }

        return false;
    }
}
