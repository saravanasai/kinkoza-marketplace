<section class="space-y-6 p-4 sm:p-6 lg:p-8">
    <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900/80">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">{{ __('View listing') }}</p>

                <h1 class="text-2xl font-semibold tracking-tight text-zinc-950 dark:text-white">
                    {{ __('View listing') }}
                </h1>

                <p class="max-w-2xl text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                    {{ __('Read-only details for :company. Ownership stays tied to your company on the server.', ['company' => $companyName]) }}
                </p>
            </div>

            <flux:button as="a" :href="route('listings.index')" wire:navigate variant="ghost" class="justify-center lg:justify-start">
                {{ __('Back') }}
            </flux:button>
        </div>
    </div>

    <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900/80">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="md:col-span-2 space-y-2">
                <label for="company" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Company') }}</label>
                <input id="company" type="text" value="{{ $companyName }}" disabled class="block w-full rounded-xl border border-zinc-300 bg-zinc-100 px-3 py-2 text-sm text-zinc-950 shadow-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
            </div>

            <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-950/40">
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Contact reveals') }}</p>
                <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">{{ $contactRevealsCount }}</p>
            </div>

            <div class="md:col-span-2 space-y-4">
                @if (count($images) > 0)
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="text-sm font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">{{ __('Current images') }}</h2>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Read-only image previews.') }}</p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            @foreach ($images as $image)
                                <figure wire:key="existing-image-{{ $image['id'] }}" class="overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-50 shadow-sm dark:border-zinc-700 dark:bg-zinc-950/40">
                                    <div class="h-40 w-full overflow-hidden bg-zinc-100 dark:bg-zinc-900">
                                        <img src="{{ $image['url'] }}" alt="{{ $image['name'] }}" class="h-full w-full object-cover object-center">
                                    </div>

                                    <figcaption class="truncate px-3 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                        {{ $image['name'] }}
                                    </figcaption>
                                </figure>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 p-6 text-center dark:border-zinc-700 dark:bg-zinc-950/40">
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('No images attached to this listing.') }}</p>
                    </div>
                @endif
            </div>

            <div class="space-y-2">
                <label for="title" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Title') }}</label>
                <input id="title" type="text" value="{{ $listing->title }}" disabled class="block w-full rounded-xl border border-zinc-300 bg-zinc-100 px-3 py-2 text-sm text-zinc-950 shadow-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
            </div>

            <div class="space-y-2 md:col-span-2">
                <label for="description" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Description') }}</label>
                <textarea id="description" rows="6" disabled class="block w-full rounded-xl border border-zinc-300 bg-zinc-100 px-3 py-2 text-sm text-zinc-950 shadow-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">{{ $listing->description }}</textarea>
            </div>

            <div class="space-y-2">
                <label for="category" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Category') }}</label>
                <input id="category" type="text" value="{{ $listing->category->value }}" disabled class="block w-full rounded-xl border border-zinc-300 bg-zinc-100 px-3 py-2 text-sm text-zinc-950 shadow-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
            </div>

            <div class="space-y-2">
                <label for="price" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Price') }}</label>
                <input id="price" type="text" value="{{ number_format($listing->price) }}" disabled class="block w-full rounded-xl border border-zinc-300 bg-zinc-100 px-3 py-2 text-sm text-zinc-950 shadow-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
            </div>

            <div class="space-y-2">
                <label for="currency" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Currency') }}</label>
                <input id="currency" type="text" value="{{ $listing->currency->value }}" disabled class="block w-full rounded-xl border border-zinc-300 bg-zinc-100 px-3 py-2 text-sm text-zinc-950 shadow-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
            </div>

            <div class="space-y-2">
                <label for="country" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Country') }}</label>
                <input id="country" type="text" value="{{ $listing->country->value }}" disabled class="block w-full rounded-xl border border-zinc-300 bg-zinc-100 px-3 py-2 text-sm text-zinc-950 shadow-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
            </div>

            <div class="space-y-2">
                <label for="city" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('City') }}</label>
                <input id="city" type="text" value="{{ $listing->city }}" disabled class="block w-full rounded-xl border border-zinc-300 bg-zinc-100 px-3 py-2 text-sm text-zinc-950 shadow-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
            </div>

            <div class="space-y-2">
                <label for="status" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Status') }}</label>
                <input id="status" type="text" value="{{ str($listing->status->value)->headline() }}" disabled class="block w-full rounded-xl border border-zinc-300 bg-zinc-100 px-3 py-2 text-sm text-zinc-950 shadow-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
            </div>

            <div class="space-y-2">
                <label for="publishedAt" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Published date') }}</label>
                <input id="publishedAt" type="text" value="{{ $listing->published_at?->format('M j, Y') ?? '—' }}" disabled class="block w-full rounded-xl border border-zinc-300 bg-zinc-100 px-3 py-2 text-sm text-zinc-950 shadow-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
            </div>

            <div class="space-y-2">
                <label for="expiresAt" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Expiry date') }}</label>
                <input id="expiresAt" type="text" value="{{ $listing->expires_at?->format('M j, Y') ?? '—' }}" disabled class="block w-full rounded-xl border border-zinc-300 bg-zinc-100 px-3 py-2 text-sm text-zinc-950 shadow-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
            </div>
        </div>
    </div>
</section>
