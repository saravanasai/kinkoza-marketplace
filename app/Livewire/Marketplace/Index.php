<?php

namespace App\Livewire\Marketplace;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\RateLimiter;

use App\Enums\Country;
use App\Enums\ListingCategory;
use App\Enums\ListingStatus;
use App\Models\Listing;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
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

    private const SEARCH_DECAY_SECONDS = 60;
    private const PER_PAGE = 12;
    private const SORT_RELEVANCE = 'relevance';
    private const SORT_NEWEST = 'newest';
    private const SORT_PRICE_ASC = 'price_asc';
    private const SORT_PRICE_DESC = 'price_desc';

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

    #[Url]
    public string $sort = self::SORT_RELEVANCE;

    public function clearFilters(): void
    {
        $this->reset('search', 'category', 'country', 'minPrice', 'maxPrice', 'postedWithin');
        $this->reset('sort');
        $this->resetPage();
    }

    public function updating(string $property): void
    {
        if (in_array($property, ['category', 'country', 'minPrice', 'maxPrice', 'sort'], true)) {
            $this->resetPage();
        }
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['category', 'country', 'minPrice', 'maxPrice', 'sort'], true)) {
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
            || $this->postedWithin !== ''
            || $this->sort !== self::SORT_RELEVANCE;
    }

    public function render(): View
    {
        $isTrottleLimitReached = false;
        $key = 'marketplace-search:' . request()->ip();
        if (! RateLimiter::attempt($key, 30, fn(): bool => true, self::SEARCH_DECAY_SECONDS)) {
            $this->addError('search', __('Please slow down your search requests.'));
            $isTrottleLimitReached = true;
        }

        $postedAfter = $this->postedAfter();
        $listings =  $isTrottleLimitReached ? collect([]) : $this->searchListings($postedAfter);

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
        $searchTerm = filled($this->search) ? $this->search : '*';

        /** @var LengthAwarePaginator $listings */
        $listings = Listing::search($searchTerm)
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
            ->when($category !== null, fn($query) => $query->where('category', $category->value))
            ->when($country !== null, fn($query) => $query->where('country', $country->value))
            ->when($this->minPrice !== null, fn($query) => $query->where('price', '>=', $this->minPrice))
            ->when($this->maxPrice !== null, fn($query) => $query->where('price', '<=', $this->maxPrice))
            ->when($this->sort === self::SORT_NEWEST, fn($query) => $query->orderBy('created_at', 'desc'))
            ->when($this->sort === self::SORT_PRICE_ASC, fn($query) => $query->orderBy('price', 'asc'))
            ->when($this->sort === self::SORT_PRICE_DESC, fn($query) => $query->orderBy('price', 'desc'))
            ->paginate(self::PER_PAGE);

        $collection = $listings->getCollection();
        $collection->load('media');
        $listings->setCollection($collection);

        return $listings;
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

    public function queryStringForListing(): array
    {
        return array_filter([
            'search' => $this->search,
            'category' => $this->category,
            'country' => $this->country,
            'minPrice' => $this->minPrice,
            'maxPrice' => $this->maxPrice,
            'postedWithin' => $this->postedWithin !== '' ? $this->postedWithin : null,
            'sort' => $this->sort !== self::SORT_RELEVANCE ? $this->sort : null,
        ], static fn($value) => $value !== null && $value !== '');
    }

    protected function rules(): array
    {
        return [
            'category' => ['nullable', Rule::enum(ListingCategory::class)],
            'country' => ['nullable', Rule::enum(Country::class)],
            'minPrice' => ['nullable', 'numeric', 'min:0'],
            'maxPrice' => ['nullable', 'numeric', 'min:0', 'gte:minPrice'],
            'postedWithin' => ['nullable', Rule::in(['7', '15', '30'])],
            'sort' => ['nullable', Rule::in([
                self::SORT_RELEVANCE,
                self::SORT_NEWEST,
                self::SORT_PRICE_ASC,
                self::SORT_PRICE_DESC,
            ])],
        ];
    }
}
