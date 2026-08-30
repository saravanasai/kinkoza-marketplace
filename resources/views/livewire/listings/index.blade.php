<div class="space-y-6 p-4 sm:p-6 lg:p-8">
    <section class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900/80">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-2">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">
                    {{ __('Listings') }}
                </p>
                <h1 class="text-lg font-semibold tracking-tight text-zinc-950 dark:text-white">
                    {{ $companyName }}
                </h1>
            </div>

            <flux:button as="a" :href="route('listings.create')" wire:navigate variant="primary" icon="plus"
                class="w-full items-center justify-center lg:w-auto">
                {{ __('Create Listing') }}
            </flux:button>
        </div>
    </section>

    <section class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900/80">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="space-y-2">
                <label for="search"
                    class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Search') }}</label>
                <input id="search" type="search" wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search by title') }}"
                    class="block w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 shadow-sm outline-none transition placeholder:text-zinc-400 focus:border-zinc-950 focus:ring-2 focus:ring-zinc-950/10 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-white dark:focus:ring-white/10">
            </div>

            <div class="space-y-2">
                <label for="status"
                    class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Status') }}</label>
                <select id="status" wire:model.live="status"
                    class="block w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 shadow-sm outline-none transition focus:border-zinc-950 focus:ring-2 focus:ring-zinc-950/10 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:focus:border-white dark:focus:ring-white/10">
                    <option value="">{{ __('All statuses') }}</option>
                    @foreach ($statusOptions as $option)
                        <option value="{{ $option->value }}">{{ str($option->value)->headline() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-2">
                <label for="category"
                    class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Category') }}</label>
                <select id="category" wire:model.live="category"
                    class="block w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 shadow-sm outline-none transition focus:border-zinc-950 focus:ring-2 focus:ring-zinc-950/10 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:focus:border-white dark:focus:ring-white/10">
                    <option value="">{{ __('All categories') }}</option>
                    @foreach ($categoryOptions as $option)
                        <option value="{{ $option->value }}">{{ $option->value }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-2">
                <label for="country"
                    class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Country') }}</label>
                <select id="country" wire:model.live="country"
                    class="block w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 shadow-sm outline-none transition focus:border-zinc-950 focus:ring-2 focus:ring-zinc-950/10 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:focus:border-white dark:focus:ring-white/10">
                    <option value="">{{ __('All countries') }}</option>
                    @foreach ($countryOptions as $option)
                        <option value="{{ $option->value }}">{{ $option->value }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-4 flex justify-end">
            <flux:button type="button" variant="primary" icon="x-mark" wire:click="clearFilter" wire:loading.attr="disabled"
                wire:target="search,status,category,country">
                {{ __('Clear filters') }}
            </flux:button>
        </div>
    </section>

    <section class="rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900/80">
        <div wire:loading.class="opacity-60" wire:target="search,status,category,country" class="p-5">
            @if ($listings->isEmpty())
                <div
                    class="rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 p-8 text-center dark:border-zinc-700 dark:bg-zinc-950/40">
                    <p class="text-lg font-semibold text-zinc-950 dark:text-white">{{ __('No listings yet.') }}</p>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Create your first listing.') }}</p>
                </div>
            @else
                <div class="hidden overflow-hidden rounded-2xl border border-zinc-200 md:block dark:border-zinc-700">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                            <thead class="bg-zinc-50 dark:bg-zinc-950/50">
                                <tr
                                    class="text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                    <th class="px-4 py-3">{{ __('Title') }}</th>
                                    <th class="px-4 py-3">{{ __('Category') }}</th>
                                    <th class="px-4 py-3">{{ __('Country') }}</th>
                                    <th class="px-4 py-3">{{ __('Status') }}</th>
                                    <th class="px-4 py-3">{{ __('Published') }}</th>
                                    <th class="px-4 py-3">{{ __('Expires') }}</th>
                                    <th class="px-4 py-3">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-900">
                                @foreach ($listings as $listing)
                                    @php
                                        $statusLabel = $listing->status;
                                        $statusBadgeClasses = match ($listing->status) {
                                            \App\Enums\ListingStatus::Draft => 'bg-zinc-100 text-zinc-700 ring-zinc-600/10 dark:bg-zinc-800 dark:text-zinc-300 dark:ring-zinc-500/20',
                                            \App\Enums\ListingStatus::PendingReview => 'bg-amber-50 text-amber-700 ring-amber-600/15 dark:bg-amber-500/10 dark:text-amber-300 dark:ring-amber-400/20',
                                            \App\Enums\ListingStatus::Published => 'bg-emerald-50 text-emerald-700 ring-emerald-600/15 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-400/20',
                                            \App\Enums\ListingStatus::Expired => 'bg-rose-50 text-rose-700 ring-rose-600/15 dark:bg-rose-500/10 dark:text-rose-300 dark:ring-rose-400/20',
                                        };
                                    @endphp

                                    <tr wire:key="listing-{{ $listing->id }}"
                                        class="text-sm text-zinc-700 dark:text-zinc-300">
                                        <td class="px-4 py-4 font-medium text-zinc-950 dark:text-white">
                                            {{ $listing->title }}</td>
                                        <td class="px-4 py-4">{{ $listing->category->value }}</td>
                                        <td class="px-4 py-4">{{ $listing->country->value }}</td>
                                        <td class="px-4 py-4">
                                            <flux:badge size="sm" class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusBadgeClasses }}">
                                                {{ $statusLabel }}
                                            </flux:badge>
                                        </td>
                                        <td class="px-4 py-4">{{ $listing->published_at?->format('M j, Y') ?? '—' }}
                                        </td>
                                        <td class="px-4 py-4">{{ $listing->expires_at?->format('M j, Y') ?? '—' }}</td>
                                        <td class="px-4 py-4">
                                            <div class="flex flex-wrap gap-2">
                                                <flux:button as="a" :href="route('listings.edit', $listing)"
                                                    wire:navigate size="sm" variant="ghost" icon="pencil-square">
                                                    {{ __('Edit') }}
                                                </flux:button>
                                                <flux:button as="a" :href="route('listings.show', $listing)"
                                                    wire:navigate size="sm" variant="ghost" icon="eye">
                                                    {{ __('View') }}
                                                </flux:button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
{{-- Mobile responsive view of the listings table --}}
                <div class="space-y-4 md:hidden">
                    @foreach ($listings as $listing)
                        @php
                            $statusLabel = $listing->status;
                            $statusBadgeClasses = match ($listing->status) {
                                \App\Enums\ListingStatus::Draft => 'bg-zinc-100 text-zinc-700 ring-zinc-600/10 dark:bg-zinc-800 dark:text-zinc-300 dark:ring-zinc-500/20',
                                \App\Enums\ListingStatus::PendingReview => 'bg-amber-50 text-amber-700 ring-amber-600/15 dark:bg-amber-500/10 dark:text-amber-300 dark:ring-amber-400/20',
                                \App\Enums\ListingStatus::Published => 'bg-emerald-50 text-emerald-700 ring-emerald-600/15 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-400/20',
                                \App\Enums\ListingStatus::Expired => 'bg-rose-50 text-rose-700 ring-rose-600/15 dark:bg-rose-500/10 dark:text-rose-300 dark:ring-rose-400/20',
                            };
                        @endphp

                        <article wire:key="mobile-listing-{{ $listing->id }}"
                            class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-950/40">
                            <div class="space-y-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 space-y-1">
                                        <h2 class="truncate text-base font-semibold text-zinc-950 dark:text-white">
                                            {{ $listing->title }}</h2>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                            {{ $listing->category->value }}</p>
                                    </div>

                                    <flux:badge size="sm" class="shrink-0 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusBadgeClasses }}">
                                        {{ $statusLabel }}
                                    </flux:badge>
                                </div>
                            </div>

                            <dl class="mt-5 grid grid-cols-2 gap-x-4 gap-y-5 border-y border-zinc-200 py-4 text-sm dark:border-zinc-700">
                                <div class="min-w-0">
                                    <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Country') }}</dt>
                                    <dd class="mt-1 font-medium text-zinc-950 dark:text-white">
                                        {{ $listing->country->value }}</dd>
                                </div>
                                <div class="min-w-0">
                                    <dt class="text-zinc-500 dark:text-zinc-400">{{ __('City') }}</dt>
                                    <dd class="mt-1 wrap-break-word font-medium text-zinc-950 dark:text-white">{{ $listing->city }}
                                    </dd>
                                </div>
                                <div class="min-w-0">
                                    <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Published') }}</dt>
                                    <dd class="mt-1 font-medium text-zinc-950 dark:text-white">
                                        {{ $listing->published_at?->format('M j, Y') ?? '—' }}</dd>
                                </div>
                                <div class="min-w-0">
                                    <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Expires') }}</dt>
                                    <dd class="mt-1 font-medium text-zinc-950 dark:text-white">
                                        {{ $listing->expires_at?->format('M j, Y') ?? '—' }}</dd>
                                </div>
                            </dl>

                            <div class="mt-4 grid grid-cols-2 gap-3">
                                <flux:button as="a" :href="route('listings.edit', $listing)" wire:navigate
                                    size="sm" variant="ghost" icon="pencil-square" class="w-full justify-center">
                                    {{ __('Edit') }}
                                </flux:button>
                                <flux:button as="a" :href="route('listings.show', $listing)" wire:navigate
                                    size="sm" variant="primary" icon="eye" class="w-full justify-center">
                                    {{ __('View') }}
                                </flux:button>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-5">
                    {{ $listings->links() }}
                </div>
            @endif
        </div>
    </section>
</div>
