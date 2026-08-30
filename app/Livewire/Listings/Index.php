<?php

namespace App\Livewire\Listings;

use App\Enums\Country;
use App\Enums\ListingCategory;
use App\Enums\ListingStatus;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Listings')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $category = '';

    #[Url]
    public string $country = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    public function updatingCountry(): void
    {
        $this->resetPage();
    }

    public function clearFilter(): void
    {
        $this->reset(['search', 'status', 'category', 'country']);
    }

    public function render(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $listings = Listing::query()
            ->where('company_id', $user->company_id)
            ->when($this->search !== '', function ($query): void {
                $query->where('title', 'like', '%'.$this->search.'%');
            })
            ->when($this->status !== '', function ($query): void {
                $query->where('status', $this->status);
            })
            ->when($this->category !== '', function ($query): void {
                $query->where('category', $this->category);
            })
            ->when($this->country !== '', function ($query): void {
                $query->where('country', $this->country);
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.listings.index', [
            'companyName' => $user->company->name,
            'listings' => $listings,
            'statusOptions' => ListingStatus::cases(),
            'categoryOptions' => ListingCategory::cases(),
            'countryOptions' => Country::cases(),
        ]);
    }
}
