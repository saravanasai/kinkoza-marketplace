<main class="marketplace-theme bg-slate-50 text-slate-900">
    <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between gap-3">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-medium text-blue-700 transition hover:text-blue-600" wire:navigate>
                <span aria-hidden="true">←</span>
                {{ __('Back to listings') }}
            </a>
            <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-emerald-700">
                {{ __('Live listing') }}
            </span>
        </div>

        <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="bg-linear-to-r from-blue-600 via-blue-600 to-blue-500 px-6 py-8 text-white sm:px-8">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-100">{{ $listing->category->value }}</p>
                        <h1 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">{{ $listing->title }}</h1>
                    </div>
                    <div class="rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-sm font-medium text-blue-50">
                        {{ $listing->company?->name ?? __('Verified seller') }}
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-blue-100">
                    <span>{{ $listing->city }}</span>
                    <span>•</span>
                    <span>{{ $listing->country->value }}</span>
                </div>
            </div>

            <div id="listing-gallery" class="border-b border-slate-200 bg-slate-100 p-4 sm:p-6">
                <p class="mb-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Gallery') }}</p>

                @if (count($images) > 0)
                    <div class="grid gap-3 md:grid-cols-4">
                        @foreach ($images as $image)
                            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                                <img src="{{ $image['url'] }}" alt="{{ $image['name'] }}" class="h-52 w-full object-cover object-center transition duration-300 hover:scale-[1.02]">
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-6 text-center text-sm text-slate-600">
                        {{ __('No photos have been uploaded for this listing yet.') }}
                    </div>
                @endif
            </div>

            <div class="grid gap-8 p-6 sm:p-8 lg:grid-cols-[1.6fr_0.9fr]">
                <div class="space-y-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Overview') }}</p>
                        <p class="mt-3 text-base leading-7 text-slate-700">{{ $listing->description }}</p>
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
                            <div>
                                <dt class="text-sm text-slate-500">{{ __('Expires') }}</dt>
                                <dd class="mt-1 font-medium text-slate-900">{{ $listing->expires_at?->format('M j, Y') ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <aside class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Price') }}</p>
                    <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">
                        {{ $listing->currency->value }} {{ number_format($listing->price) }}
                    </p>

                    <div class="mt-5 rounded-xl border border-slate-200 bg-white p-4 text-sm text-slate-600">
                        {{ __('This listing is currently live on the marketplace.') }}
                    </div>

                    @auth
                        @if ($contactRevealed)
                            <div class="mt-5 space-y-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">{{ __('Seller contact') }}</p>
                                <dl class="space-y-3">
                                    @if ($listing->company?->contact_email)
                                        <div>
                                            <dt class="text-xs font-medium text-emerald-700">{{ __('Email') }}</dt>
                                            <dd class="mt-1"><a href="mailto:{{ $listing->company->contact_email }}" class="font-medium hover:text-emerald-700">{{ $listing->company->contact_email }}</a></dd>
                                        </div>
                                    @endif
                                    @if ($listing->company?->contact_phone)
                                        <div>
                                            <dt class="text-xs font-medium text-emerald-700">{{ __('Phone') }}</dt>
                                            <dd class="mt-1"><a href="tel:{{ $listing->company->contact_phone }}" class="font-medium hover:text-emerald-700">{{ $listing->company->contact_phone }}</a></dd>
                                        </div>
                                    @endif
                                </dl>
                                @if (! $listing->company?->contact_email && ! $listing->company?->contact_phone)
                                    <p>{{ __('The seller has not provided contact details.') }}</p>
                                @endif
                            </div>
                        @else
                            <button type="button" wire:click="revealContact" class="mt-5 inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-500">
                                {{ __('Reveal seller contact') }}
                            </button>
                            @error('contact')
                                <p class="mt-3 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="mt-5 inline-flex w-full items-center justify-center rounded-xl border border-blue-200 bg-white px-4 py-3 text-sm font-semibold text-blue-700 transition hover:border-blue-300 hover:bg-blue-50" wire:navigate>
                            {{ __('Log in to view contact details') }}
                        </a>
                    @endauth
                </aside>
            </div>
        </article>
    </section>
</main>
