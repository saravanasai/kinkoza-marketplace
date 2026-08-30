<?php

namespace App\Livewire\Marketplace;

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

    private const PER_PAGE = 12;

    #[Url]
    public string $category = '';

    #[Url]
    public string $search = '';

    #[Url]
    public string $country = '';

    #[Url]
    public string $minPrice = '';

    #[Url]
    public string $maxPrice = '';

    #[Url]
    public string $postedWithin = '';

    public function clearFilters(): void
    {
        $this->reset('search', 'category', 'country', 'minPrice', 'maxPrice', 'postedWithin');
        $this->resetPage();
    }

    public function updating(string $property): void
    {
        if (in_array($property, ['search', 'category', 'country', 'minPrice', 'maxPrice', 'postedWithin'], true)) {
            $this->resetPage();
        }
    }

    public function render(): View
    {
        $postedAfter = $this->postedAfter();
        $listings = $this->searchListings($postedAfter);

        if ($listings instanceof LengthAwarePaginator) {
            $listings->getCollection()->load('company');
        }

        return view('livewire.marketplace.index', [
            'listings' => $listings,
            'categoryOptions' => ListingCategory::cases(),
            'countryOptions' => Country::cases(),
        ]);
    }

    private function searchListings(?int $postedAfter): LengthAwarePaginator
    {
        return Listing::search($this->search)
            ->where('status', ListingStatus::Published->value)
            ->when(config('scout.driver') === 'typesense', function ($query): void {
                $query
                    ->where('published_at', '<=', now()->timestamp)
                    ->where('expires_at', '>', now()->timestamp);
            })
            ->when($postedAfter !== null, function ($query) use ($postedAfter): void {
                $query->where(
                    'published_at',
                    '>=',
                    config('scout.driver') === 'typesense' ? $postedAfter : now()->setTimestamp($postedAfter),
                );
            })
            ->when($this->category !== '', function ($query): void {
                $query->where('category', $this->category);
            })
            ->when($this->country !== '', function ($query): void {
                $query->where('country', $this->country);
            })
            ->when(is_numeric($this->minPrice), function ($query): void {
                $query->where('price', '>=', $this->minPrice);
            })
            ->when(is_numeric($this->maxPrice), function ($query): void {
                $query->where('price', '<=', $this->maxPrice);
            })
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
}
