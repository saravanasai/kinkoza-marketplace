<main class="marketplace-theme bg-slate-50 text-slate-900">
    <section class="bg-linear-to-r from-blue-600 via-blue-600 to-blue-500 text-white">
        <div class="px-4 py-10 sm:px-6 lg:px-8 lg:py-16">
            <div class="mx-auto max-w-4xl text-center">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-100">
                    {{ __('Kinkoza marketplace') }}
                </p>
                <h1 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl lg:text-5xl">
                    {{ __('Source commercial assets with confidence') }}
                </h1>
                <p class="mt-3 text-sm text-blue-100 sm:text-base">
                    {{ __('Search machinery, vehicles, property, and business equipment from vetted sellers around the world.') }}
                </p>

                <div class="mt-8 rounded-2xl border border-white/30 bg-white/10 p-3 shadow-lg backdrop-blur-sm">
                    <div
                        class="flex items-center gap-3 rounded-xl bg-white px-4 py-3 text-left text-slate-500 shadow-sm">
                        <svg class="h-5 w-5 shrink-0 text-blue-600" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="11" cy="11" r="6"></circle>
                            <path d="M16 16L21 21"></path>
                        </svg>
                        <input type="search" wire:model.live.debounce.500ms="search"
                            placeholder="{{ __('Search for machinery, vehicles, property...') }}"
                            class="w-full border-0 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none"
                            aria-label="{{ __('Search listings') }}">
                    </div>
                </div>
                @error('search')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div class="mx-auto mt-8 grid max-w-2xl gap-4 border-t border-white/30 pt-6 text-left sm:grid-cols-3">
                    <div>
                        <p class="text-2xl font-semibold tracking-tight">1M+</p>
                        <p class="mt-1 text-sm text-blue-100">{{ __('Over 1 million verified listings') }}</p>
                    </div>
                    <div>
                        <p class="text-2xl font-semibold tracking-tight">50+</p>
                        <p class="mt-1 text-sm text-blue-100">{{ __('Markets represented') }}</p>
                    </div>
                    <div>
                        <p class="text-2xl font-semibold tracking-tight">24/7</p>
                        <p class="mt-1 text-sm text-blue-100">{{ __('Global marketplace access') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="marketplace-listings" class="px-4 py-8 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="grid gap-8 lg:grid-cols-[280px_minmax(0,1fr)]">
                <div class="order-2 lg:order-2">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-slate-500">{{ __('Featured marketplace') }}</p>
                            <h2 class="mt-1 text-xl font-semibold text-slate-900">{{ __('Public listings') }}</h2>
                        </div>
                    </div>

                    @if ($listings->isEmpty())
                        <div
                            class="mt-8 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center">
                            <div
                                class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.8" aria-hidden="true">
                                    <path
                                        d="M4 7.5A2.5 2.5 0 0 1 6.5 5h11A2.5 2.5 0 0 1 20 7.5v9A2.5 2.5 0 0 1 17.5 19h-11A2.5 2.5 0 0 1 4 16.5v-9Z">
                                    </path>
                                    <path d="M8 9h8M8 13h5"></path>
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-semibold text-slate-900">
                                {{ __('No listings match this category yet') }}</h3>
                            <p class="mt-2 text-sm text-slate-600">
                                {{ __('Try another category to browse the live marketplace.') }}
                            </p>
                        </div>
                    @else
                        <div class="mt-8 grid gap-5 md:grid-cols-2 2xl:grid-cols-3">
                            @foreach ($listings as $listing)
                                @php
                                    $featuredImage = $listing->getMedia('images')->first();
                                @endphp
                                <article wire:key="listing-{{ $listing->id }}"
                                    class="flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 shadow-sm transition hover:border-blue-200 hover:shadow-md">
                                    @if ($featuredImage)
                                        <img src="{{ $featuredImage->getTemporaryUrl(now()->addMinutes(15)) }}"
                                            alt="{{ $listing->title }}" class="h-52 w-full object-cover object-center">
                                    @else
                                        <div
                                            class="flex h-52 items-center justify-center bg-linear-to-br from-blue-100 via-slate-100 to-slate-200 text-blue-700">
                                            <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                                <path
                                                    d="M4 7.5A2.5 2.5 0 0 1 6.5 5h11A2.5 2.5 0 0 1 20 7.5v9A2.5 2.5 0 0 1 17.5 19h-11A2.5 2.5 0 0 1 4 16.5v-9Z">
                                                </path>
                                                <circle cx="9" cy="10" r="2"></circle>
                                                <path d="m20 15-5-5L7 19"></path>
                                            </svg>
                                        </div>
                                    @endif

                                    <div class="flex flex-1 flex-col p-5">
                                        <div
                                            class="flex items-center justify-between gap-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">
                                            <div class="flex min-w-0 items-center gap-2">
                                                <span
                                                    class="shrink-0 whitespace-nowrap rounded-full bg-white px-2.5 py-1 text-blue-700 ring-1 ring-inset ring-blue-100">
                                                    {{ $listing->category->value }}
                                                </span>
                                                @if ($listing->published_at?->isToday())
                                                    <span
                                                        class="shrink-0 whitespace-nowrap rounded-full bg-emerald-50 px-2.5 py-1 text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                                        {{ __('Latest') }}
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="min-w-0 truncate text-right">{{ $listing->city }}</span>
                                        </div>

                                        <div class="mt-4 flex-1">
                                            <h3 class="text-xl font-semibold tracking-tight text-slate-900">
                                                {{ $listing->title }}</h3>
                                            <p class="mt-2 text-sm font-medium text-slate-600">
                                                {{ $listing->company?->name ?? __('Verified seller') }}</p>
                                            <p class="mt-3 text-sm leading-6 text-slate-600">
                                                {{ str($listing->description)->limit(140) }}</p>
                                        </div>

                                        <div class="mt-5 border-t border-slate-200 pt-4">
                                            <div class="flex items-center justify-between gap-3">
                                                <div>
                                                    <p
                                                        class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">
                                                        {{ __('Price') }}</p>
                                                    <p class="mt-1 text-lg font-semibold text-slate-900">
                                                        {{ $listing->currency->value }}
                                                       {{ \Illuminate\Support\Number::format($listing->price) }}
                                                    </p>
                                                </div>
                                                <flux:button as="a"
                                                    :href="route('marketplace.listings.show', array_merge([
                                                            'marketplaceListing' => $listing->slug,
                                                        ],$this->queryStringForListing()))"
                                                    wire:navigate size="sm" variant="primary" icon="eye"
                                                    class="min-w-36 rounded-full px-4 py-2.5 font-semibold shadow-md transition hover:-translate-y-0.5 hover:shadow-lg">
                                                    {{ __('More details') }}
                                                </flux:button>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        @if ($listings->hasPages())
                            <nav aria-label="{{ __('Listing pagination') }}"
                                class="mt-8 flex items-center justify-between gap-3 border-t border-slate-200 pt-5">
                                @if ($listings->onFirstPage())
                                    <span
                                        class="inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-400">
                                        {{ __('Previous') }}
                                    </span>
                                @else
                                    <button type="button" wire:click="previousPage"
                                        class="inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-blue-300 hover:text-blue-700">
                                        {{ __('Previous') }}
                                    </button>
                                @endif

                                @if ($listings->hasMorePages())
                                    <button type="button" wire:click="nextPage"
                                        class="inline-flex items-center rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-500">
                                        {{ __('Next') }}
                                    </button>
                                @else
                                    <span
                                        class="inline-flex items-center rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-400">
                                        {{ __('Next') }}
                                    </span>
                                @endif
                            </nav>
                        @endif
                    @endif
                </div>

                <aside
                    class="order-1 rounded-2xl border border-slate-200 bg-slate-50 p-5 lg:order-1 lg:sticky lg:top-6 lg:self-start">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                            <flux:icon name="funnel" class="h-4 w-4 text-slate-900" />
                            <span>{{ __('Filters') }}</span>
                        </h3>
                        @if ($this->isFilterApplied())
                            <flux:button type="button" variant="primary" size="sm" icon="x-mark"
                                wire:click="clearFilters" class="rounded-full">
                                {{ __('Clear filter') }}
                            </flux:button>
                        @endif
                    </div>

                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="category"
                                class="mb-2 block text-sm font-medium text-slate-700">{{ __('Category') }}</label>
                            <select id="category" wire:model.live="category" @class([
                                'w-full rounded-xl border bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:outline-none focus:ring-2',
                                'border-slate-300 focus:border-blue-500 focus:ring-blue-200' => !$errors->has(
                                    'category'),
                                'border-red-400 focus:border-red-500 focus:ring-red-200' => $errors->has(
                                    'category'),
                            ])>
                                <option value="">{{ __('All categories') }}</option>
                                @foreach ($categoryOptions as $option)
                                    <option value="{{ $option->value }}">{{ $option->value }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="country"
                                class="mb-2 block text-sm font-medium text-slate-700">{{ __('Country') }}</label>
                            <select id="country" wire:model.live="country" @class([
                                'w-full rounded-xl border bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:outline-none focus:ring-2',
                                'border-slate-300 focus:border-blue-500 focus:ring-blue-200' => !$errors->has(
                                    'country'),
                                'border-red-400 focus:border-red-500 focus:ring-red-200' => $errors->has(
                                    'country'),
                            ])>
                                <option value="">{{ __('All countries') }}</option>
                                @foreach ($countryOptions as $option)
                                    <option value="{{ $option->value }}">{{ $option->value }}</option>
                                @endforeach
                            </select>
                            @error('country')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="posted-within"
                                class="mb-2 block text-sm font-medium text-slate-700">{{ __('Date posted') }}</label>
                            <select id="posted-within" wire:model.live="postedWithin"
                                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                <option value="">{{ __('Any time') }}</option>
                                <option value="7">{{ __('Last 7 days') }}</option>
                                <option value="15">{{ __('Last 15 days') }}</option>
                                <option value="30">{{ __('Last 30 days') }}</option>
                            </select>
                        </div>

                        <div>
                            <label for="sort"
                                class="mb-2 block text-sm font-medium text-slate-700">{{ __('Sort by') }}</label>
                            <select id="sort" wire:model.live="sort"
                                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                <option value="relevance">{{ __('Relevance') }}</option>
                                <option value="newest">{{ __('Newest first') }}</option>
                                <option value="price_asc">{{ __('Price: low to high') }}</option>
                                <option value="price_desc">{{ __('Price: high to low') }}</option>
                            </select>
                        </div>

                        <fieldset>
                            <legend class="mb-2 block text-sm font-medium text-slate-700">{{ __('Price range') }}
                            </legend>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <input id="min-price" type="number" min="0" step="1"
                                        wire:model.live.debounce.500ms="minPrice" placeholder="{{ __('Min') }}"
                                        aria-label="{{ __('Minimum price') }}" @class([
                                            'w-full rounded-xl border bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:outline-none focus:ring-2',
                                            'border-slate-300 focus:border-blue-500 focus:ring-blue-200' => !$errors->has(
                                                'minPrice'),
                                            'border-red-400 focus:border-red-500 focus:ring-red-200' => $errors->has(
                                                'minPrice'),
                                        ])>
                                    @error('minPrice')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <input id="max-price" type="number" min="0" step="1"
                                        wire:model.live.debounce.500ms="maxPrice" placeholder="{{ __('Max') }}"
                                        aria-label="{{ __('Maximum price') }}" @class([
                                            'w-full rounded-xl border bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:outline-none focus:ring-2',
                                            'border-slate-300 focus:border-blue-500 focus:ring-blue-200' => !$errors->has(
                                                'maxPrice'),
                                            'border-red-400 focus:border-red-500 focus:ring-red-200' => $errors->has(
                                                'maxPrice'),
                                        ])>
                                    @error('maxPrice')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </fieldset>

                        @if ($this->isFilterApplied())
                            <div class="rounded-xl border border-blue-200 bg-blue-50 px-3 py-2 text-sm text-blue-700">
                                {{ __('Filters applied') }}
                            </div>
                        @endif

                        <div class="rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-600">
                            {{ __('Browse live inventory from verified sellers and narrow the feed to the category you need.') }}
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</main>
