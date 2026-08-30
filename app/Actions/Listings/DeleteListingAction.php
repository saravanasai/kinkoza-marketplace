<?php

namespace App\Actions\Listings;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteListingAction
{
    use AsAction;

    public function handle(User $user, Listing $listing): void
    {
        Gate::forUser($user)->authorize('delete', $listing);

        $listing->delete();
    }
}
