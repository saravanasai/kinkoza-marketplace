<?php

namespace App\Livewire\Marketplace;

use App\Enums\Country;
use App\Enums\ListingCategory;
use App\Enums\ListingStatus;
use App\Models\Listing;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Kinkoza marketplace')]
#[Layout('layouts.marketplace')]
class Index extends Component
{
    use WithPagination;

    private const PER_PAGE = 12;

    #[Url]
    public ?string $category = null;

    #[Url]
    public ?string $search = null;

    #[Url]
    public ?string $country = null;

    #[Url]
    public ?int $minPrice = null;

    #[Url]
    public ?int $maxPrice = null;

    #[Url]
    public string $postedWithin = '';

    public function clearFilters(): void
    {
        $this->reset('search', 'category', 'country', 'minPrice', 'maxPrice', 'postedWithin');
        $this->resetPage();
    }

    public function updating(string $property): void
    {
        if (in_array($property, ['category', 'country', 'minPrice', 'maxPrice'], true)) {
            $this->resetPage();
        }
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['category', 'country', 'minPrice', 'maxPrice'], true)) {
            $this->validateOnly($property);
        }
    }

    public function isFilterApplied(): bool
    {
        return ($this->search !== null && $this->search !== '')
            || $this->category !== null
            || $this->country !== null
            || $this->minPrice !== null
            || $this->maxPrice !== null
            || $this->postedWithin !== '';
    }

    public function render(): View
    {
        $postedAfter = $this->postedAfter();
        $listings = $this->searchListings($postedAfter);

        return view('livewire.marketplace.index', [
            'listings' => $listings,
            'categoryOptions' => ListingCategory::cases(),
            'countryOptions' => Country::cases(),
        ]);
    }

    private function searchListings(?int $postedAfter): LengthAwarePaginator
    {

        $category = $this->category !== null ? ListingCategory::tryFrom($this->category) : null;
        $country = $this->country !== null ? Country::tryFrom($this->country) : null;


        return Listing::search($this->search)
            ->where('status', ListingStatus::Published->value)
            ->where('published_at', '<=', now()->timestamp)
            ->where('expires_at', '>', now()->timestamp)
            ->when($postedAfter !== null, function ($query) use ($postedAfter): void {
                $query->where(
                    'published_at',
                    '>=',
                    $postedAfter,
                );
            })
            ->when($category !== null, fn ($query) => $query->where('category', $category->value))
            ->when($country !== null, fn ($query) => $query->where('country', $country->value))
            ->when($this->minPrice !== null, fn ($query) => $query->where('price', '>=', $this->minPrice))
            ->when($this->maxPrice !== null, fn ($query) => $query->where('price', '<=', $this->maxPrice))
            ->orderBy('created_at', 'desc')
            ->paginate(self::PER_PAGE);
    }

    private function postedAfter(): ?int
    {
        $days = match ($this->postedWithin) {
            '7' => 7,
            '15' => 15,
            '30' => 30,
            default => null,
        };

        return $days === null ? null : now()->subDays($days)->timestamp;
    }

    protected function rules(): array
    {
        return [
            'category' => ['nullable', Rule::enum(ListingCategory::class)],
            'country' => ['nullable', Rule::enum(Country::class)],
            'minPrice' => ['nullable', 'numeric', 'min:0'],
            'maxPrice' => ['nullable', 'numeric', 'min:0', 'gte:minPrice'],
            'postedWithin' => ['nullable', Rule::in(['7', '15', '30'])],
        ];
    }
}
