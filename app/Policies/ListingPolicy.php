<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\Listing;
use App\Models\User;

class ListingPolicy
{
    public function view(User $user, Listing $listing): bool
    {
        return $user->company_id === $listing->company_id;
    }

    public function create(User $user, Company $company): bool
    {
        return $user->company_id === $company->id;
    }

    public function update(User $user, Listing $listing): bool
    {
        return $user->company_id === $listing->company_id;
    }

    public function delete(User $user, Listing $listing): bool
    {
        return $user->company_id === $listing->company_id;
    }

    public function publish(User $user, Listing $listing): bool
    {
        return $user->company_id === $listing->company_id;
    }
}
