<?php

use App\Models\Company;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

test('seller A can manage Listing A', function () {

    $sellerA = User::factory()->create();
    $listingA = Listing::factory()->for($sellerA->company)->create();

    expect(Gate::forUser($sellerA)->allows('view', $listingA))->toBeTrue()
        ->and(Gate::forUser($sellerA)->allows('update', $listingA))->toBeTrue()
        ->and(Gate::forUser($sellerA)->allows('delete', $listingA))->toBeTrue()
        ->and(Gate::forUser($sellerA)->allows('publish', $listingA))->toBeTrue()
        ->and(Gate::forUser($sellerA)->allows('create', [Listing::class, $sellerA->company]))->toBeTrue();
});

test('seller A cannot update Listing B', function () {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();
    $sellerA = User::factory()->forCompany($companyA)->create();
    $listingB = Listing::factory()->for($companyB)->create();

    expect(Gate::forUser($sellerA)->allows('update', $listingB))->toBeFalse();
});

test('seller A cannot delete Listing B', function () {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();
    $sellerA = User::factory()->forCompany($companyA)->create();
    $listingB = Listing::factory()->for($companyB)->create();

    expect(Gate::forUser($sellerA)->allows('delete', $listingB))->toBeFalse();
});

test('seller A cannot publish Listing B', function () {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();
    $sellerA = User::factory()->forCompany($companyA)->create();
    $listingB = Listing::factory()->for($companyB)->create();

    expect(Gate::forUser($sellerA)->allows('publish', $listingB))->toBeFalse();
});

test('seller A cannot access Company B where the policy denies access', function () {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();
    $sellerA = User::factory()->forCompany($companyA)->create();

    expect(Gate::forUser($sellerA)->allows('view', $companyB))->toBeFalse()
        ->and(Gate::forUser($sellerA)->allows('update', $companyB))->toBeFalse()
        ->and(Gate::forUser($sellerA)->allows('delete', $companyB))->toBeFalse();
});
