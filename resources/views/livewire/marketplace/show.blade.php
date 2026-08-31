@php
    $pageTitle = $listing->title.' - '.config('app.name', 'Kinkoza Marketplace');
    $pageDescription = str($listing->description)->stripTags()->squish()->limit(160);
    $canonicalUrl = route('marketplace.listings.show', $listing);
    $ogImage = $images[0]['url'] ?? asset('android-chrome-512x512.png');
@endphp

@push('head')
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    <meta property="og:type" content="product">
    <meta property="og:site_name" content="{{ config('app.name', 'Kinkoza Marketplace') }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:alt" content="{{ $listing->title }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $listing->title,
            'description' => $pageDescription,
            'url' => $canonicalUrl,
            'image' => $ogImage,
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => $listing->currency->value,
                'price' => $listing->price,
                'availability' => 'https://schema.org/InStock',
            ],
            'brand' => [
                '@type' => 'Organization',
                'name' => $listing->company?->name ?? __('Verified seller'),
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endpush

<main class="marketplace-theme bg-slate-50 text-slate-900">
    <section class="px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
        <div class="mb-6 flex items-center justify-between gap-3">
            <flux:button as="a" :href="route('home')" wire:navigate variant="ghost" icon="arrow-left" class="justify-center">
                {{ __('Back to marketplace') }}
            </flux:button>
            <div class="flex flex-wrap items-center gap-2">
                @if ($isOwnListing)
                    <span class="inline-flex rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-blue-700">
                        {{ __('Own listing') }}
                    </span>
                @endif
                <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-emerald-700">
                    {{ __('Live listing') }}
                </span>
            </div>
        </div>

        <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <header class="bg-linear-to-r from-blue-600 via-blue-600 to-blue-500 px-6 py-8 text-white sm:px-8 lg:px-10 lg:py-10">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-4xl">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-100">{{ $listing->category->value }}</p>
                        <h1 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl lg:text-5xl">{{ $listing->title }}</h1>
                        <div class="mt-4 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-blue-100">
                            <span>{{ $listing->city }}</span>
                            <span aria-hidden="true">•</span>
                            <span>{{ $listing->country->value }}</span>
                        </div>
                    </div>
                    <div class="max-w-full self-start whitespace-nowrap rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-sm font-medium text-blue-50 lg:self-auto">
                        {{ $listing->company?->name ?? __('Verified seller') }}
                    </div>
                </div>
            </header>

            <section id="listing-gallery" class="border-b border-slate-200 bg-slate-100 p-4 sm:p-6 lg:p-8">
                <p class="mb-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Gallery') }}</p>

                @if (count($images) > 0)
                    <div class="grid auto-rows-48 gap-3 sm:grid-cols-2 lg:grid-cols-4 lg:auto-rows-60">
                        @foreach ($images as $image)
                            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm {{ $loop->first ? 'sm:col-span-2 lg:row-span-2' : '' }}">
                                <img src="{{ $image['url'] }}" alt="{{ $image['name'] }}" class="h-full w-full object-cover object-center transition duration-300 hover:scale-[1.02]">
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-6 text-center text-sm text-slate-600">
                        {{ __('No photos have been uploaded for this listing yet.') }}
                    </div>
                @endif
            </section>

            <div class="grid gap-8 p-6 sm:p-8 lg:grid-cols-[minmax(0,1.6fr)_minmax(20rem,0.85fr)] lg:p-10">
                <aside class="order-1 rounded-2xl border border-slate-200 bg-slate-50 p-5 sm:p-6 lg:order-2 lg:sticky lg:top-6 lg:self-start">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Price') }}</p>
                    <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">
                        {{ $listing->currency->value }} {{ number_format($listing->price) }}
                    </p>

                    <div class="mt-6 rounded-xl border border-slate-200 bg-white p-4 text-sm leading-6 text-slate-600">
                        @if ($isOwnListing)
                            {{ __('This is your own marketplace listing, so contact details are shown automatically.') }}
                        @else
                            {{ __('Explore more details on this marketplace listing.') }}
                        @endif
                    </div>

                    @auth
                        @if ($contactRevealed)
                            <div class="mt-5 space-y-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">
                                    {{ $isOwnListing ? __('Your company contact') : __('Seller contact') }}
                                </p>
                                <dl class="space-y-3">
                                    @if ($listing->company?->contact_email)
                                        <div>
                                            <dt class="text-xs font-medium text-emerald-700">{{ __('Email') }}</dt>
                                            <dd class="mt-1 wrap-break-word"><a href="mailto:{{ $listing->company->contact_email }}" class="font-medium hover:text-emerald-700">{{ $listing->company->contact_email }}</a></dd>
                                        </div>
                                    @endif
                                    @if ($listing->company?->contact_phone)
                                        <div>
                                            <dt class="text-xs font-medium text-emerald-700">{{ __('Phone') }}</dt>
                                            <dd class="mt-1 wrap-break-word"><a href="tel:{{ $listing->company->contact_phone }}" class="font-medium hover:text-emerald-700">{{ $listing->company->contact_phone }}</a></dd>
                                        </div>
                                    @endif
                                </dl>
                                @if (! $listing->company?->contact_email && ! $listing->company?->contact_phone)
                                    <p>
                                        {{ $isOwnListing ? __('Your company has not provided contact details.') : __('The seller has not provided contact details.') }}
                                    </p>
                                @endif
                            </div>
                        @elseif (! $isOwnListing)
                            <button type="button" wire:click="revealContact" class="mt-5 inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-500">
                                {{ __('Contact seller') }}
                            </button>
                            @error('contact')
                                <p class="mt-3 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        @else
                            <div class="mt-5 rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
                                {{ __('Your company contact details are shown above.') }}
                            </div>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="mt-5 inline-flex w-full items-center justify-center rounded-xl border border-blue-200 bg-white px-4 py-3 text-sm font-semibold text-blue-700 transition hover:border-blue-300 hover:bg-blue-50" wire:navigate>
                            {{ __('Log in to view contact details') }}
                        </a>
                    @endauth
                </aside>

                <div class="order-2 space-y-6 lg:order-1">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Overview') }}</p>
                        <p class="mt-3 max-w-4xl text-base leading-7 text-slate-700">{{ $listing->description }}</p>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Details') }}</p>
                        <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm text-slate-500">{{ __('Country') }}</dt>
                                <dd class="mt-1 font-medium text-slate-900">{{ $listing->country->value }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-slate-500">{{ __('City') }}</dt>
                                <dd class="mt-1 font-medium text-slate-900">{{ $listing->city }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-slate-500">{{ __('Published') }}</dt>
                                <dd class="mt-1 font-medium text-slate-900">{{ $listing->published_at?->format('M j, Y') ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </article>
    </section>
</main>
