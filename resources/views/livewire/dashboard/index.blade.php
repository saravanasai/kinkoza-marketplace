<div class="space-y-6 p-4 sm:p-6 lg:p-8">
    <section class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900/80">
        <div class="border-b border-zinc-200 px-6 py-5 dark:border-zinc-700">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">
                {{ __('Seller dashboard') }}
            </p>

            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-zinc-950 dark:text-white sm:text-3xl">
                {{ $companyName }}
            </h1>

            <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                {{ __('Manage your listings, monitor their status, and keep your seller profile organized.') }}
            </p>
        </div>

        <div class="grid gap-4 p-6 sm:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('My Listings') }}</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ $myListingsCount }}</p>
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Published') }}</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ $publishedCount }}</p>
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Drafts') }}</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ $draftCount }}</p>
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Pending Review') }}</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ $pendingReviewCount }}</p>
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Contact reveals') }}</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ $contactRevealsCount }}</p>
            </div>
        </div>
    </section>
</div>
