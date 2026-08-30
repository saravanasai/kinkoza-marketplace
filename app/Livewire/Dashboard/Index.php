<?php

namespace App\Livewire\Dashboard;

use App\Enums\ListingStatus;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public string $companyName = '';

    public int $myListingsCount = 0;

    public int $publishedCount = 0;

    public int $draftCount = 0;

    public int $pendingReviewCount = 0;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $user->loadMissing('company');

        $this->companyName = $user->company->name;

        $counts = Listing::query()
            ->where('company_id', $user->company_id)
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $this->myListingsCount = (int) $counts->sum();
        $this->publishedCount = (int) ($counts[ListingStatus::Published->value] ?? 0);
        $this->draftCount = (int) ($counts[ListingStatus::Draft->value] ?? 0);
        $this->pendingReviewCount = (int) ($counts[ListingStatus::PendingReview->value] ?? 0);
    }

    public function render(): View
    {
        return view('livewire.dashboard.index');
    }
}
