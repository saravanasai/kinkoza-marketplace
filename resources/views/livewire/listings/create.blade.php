<section class="space-y-6 p-4 sm:p-6 lg:p-8">
    <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900/80">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">
            {{ __('Create listing') }}
        </p>

        <div class="mt-2 space-y-2">
            <h1 class="text-2xl font-semibold tracking-tight text-zinc-950 dark:text-white">
                {{ __('New listing') }}
            </h1>

            <p class="max-w-2xl text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                {{ __('Create a listing for :company. Ownership is derived server-side from your account.', ['company' => $companyName]) }}
            </p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900/80">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2 space-y-2">
                    <label for="company" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Company') }}</label>
                    <input id="company" type="text" value="{{ $companyName }}" disabled class="block w-full rounded-xl border border-zinc-300 bg-zinc-100 px-3 py-2 text-sm text-zinc-950 shadow-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                </div>

                <div class="space-y-2">
                    <label for="title" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Title') }}</label>
                    <input id="title" type="text" wire:model="title" class="block w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 shadow-sm outline-none transition focus:border-zinc-950 focus:ring-2 focus:ring-zinc-950/10 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:focus:border-white dark:focus:ring-white/10">
                    @error('title')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label for="description" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Description') }}</label>
                    <textarea id="description" wire:model="description" rows="6" class="block w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 shadow-sm outline-none transition focus:border-zinc-950 focus:ring-2 focus:ring-zinc-950/10 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:focus:border-white dark:focus:ring-white/10"></textarea>
                    @error('description')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label for="images" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Images') }}</label>
                    <input
                        id="images"
                        type="file"
                        wire:model="images"
                        multiple
                        accept="image/jpeg,image/png,image/webp"
                        class="block w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 shadow-sm outline-none transition file:mr-4 file:rounded-lg file:border-0 file:bg-zinc-950 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-zinc-800 focus:border-zinc-950 focus:ring-2 focus:ring-zinc-950/10 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:file:bg-white dark:file:text-zinc-950 dark:hover:file:bg-zinc-200 dark:focus:border-white dark:focus:ring-white/10"
                    >
                    @error('images')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                    @error('images.*')<p class="text-sm text-red-600">{{ $message }}</p>@enderror

                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('Add up to 4 images. JPG, PNG, and WEBP only.') }}
                    </p>

                    @if (count($images) > 0)
                        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            @foreach ($images as $index => $image)
                                <figure wire:key="image-preview-{{ $index }}" class="overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-50 shadow-sm dark:border-zinc-700 dark:bg-zinc-950/40">
                                    <div class="h-40 w-full overflow-hidden bg-zinc-100 dark:bg-zinc-900">
                                        <img src="{{ $image->temporaryUrl() }}" alt="{{ __('Image preview') }}" class="h-full w-full object-cover object-center">
                                    </div>

                                    <figcaption class="flex items-center justify-between gap-3 px-3 py-2">
                                        <span class="truncate text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $image->getClientOriginalName() }}</span>

                                        <button type="button" wire:click="removeImage({{ $index }})" class="text-sm font-medium text-red-600 hover:text-red-700">
                                            {{ __('Remove') }}
                                        </button>
                                    </figcaption>
                                </figure>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="space-y-2">
                    <label for="category" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Category') }}</label>
                    <select id="category" wire:model="category" class="block w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 shadow-sm outline-none transition focus:border-zinc-950 focus:ring-2 focus:ring-zinc-950/10 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:focus:border-white dark:focus:ring-white/10">
                        @foreach($categoryOptions as $option)
                            <option value="{{ $option->value }}">{{ $option->value }}</option>
                        @endforeach
                    </select>
                    @error('category')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label for="price" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Price') }}</label>
                    <input id="price" type="number" min="1" step="1" wire:model="price" class="block w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 shadow-sm outline-none transition focus:border-zinc-950 focus:ring-2 focus:ring-zinc-950/10 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:focus:border-white dark:focus:ring-white/10">
                    @error('price')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label for="currency" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Currency') }}</label>
                    <select id="currency" wire:model="currency" class="block w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 shadow-sm outline-none transition focus:border-zinc-950 focus:ring-2 focus:ring-zinc-950/10 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:focus:border-white dark:focus:ring-white/10">
                        @foreach($currencyOptions as $option)
                            <option value="{{ $option->value }}">{{ $option->value }}</option>
                        @endforeach
                    </select>
                    @error('currency')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label for="country" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Country') }}</label>
                    <select id="country" wire:model="country" class="block w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 shadow-sm outline-none transition focus:border-zinc-950 focus:ring-2 focus:ring-zinc-950/10 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:focus:border-white dark:focus:ring-white/10">
                        @foreach($countryOptions as $option)
                            <option value="{{ $option->value }}">{{ $option->value }}</option>
                        @endforeach
                    </select>
                    @error('country')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label for="city" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('City') }}</label>
                    <input id="city" type="text" wire:model="city" class="block w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 shadow-sm outline-none transition focus:border-zinc-950 focus:ring-2 focus:ring-zinc-950/10 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:focus:border-white dark:focus:ring-white/10">
                    @error('city')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label for="status" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Status') }}</label>
                    <select id="status" wire:model="status" class="block w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 shadow-sm outline-none transition focus:border-zinc-950 focus:ring-2 focus:ring-zinc-950/10 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:focus:border-white dark:focus:ring-white/10">
                        @foreach($statusOptions as $option)
                            <option value="{{ $option->value }}">{{ str($option->value)->headline() }}</option>
                        @endforeach
                    </select>
                    @error('status')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label for="publishedAt" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Published date') }}</label>
                    <input id="publishedAt" type="datetime-local" wire:model="publishedAt" class="block w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 shadow-sm outline-none transition focus:border-zinc-950 focus:ring-2 focus:ring-zinc-950/10 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:focus:border-white dark:focus:ring-white/10">
                    @error('publishedAt')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label for="expiresAt" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Expiry date') }}</label>
                    <input id="expiresAt" type="datetime-local" wire:model="expiresAt" class="block w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 shadow-sm outline-none transition focus:border-zinc-950 focus:ring-2 focus:ring-zinc-950/10 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:focus:border-white dark:focus:ring-white/10">
                    @error('expiresAt')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
            <flux:button as="a" :href="route('listings.index')" wire:navigate variant="ghost" icon="arrow-left" class="justify-center">
                {{ __('Cancel') }}
            </flux:button>

            <flux:button variant="primary" type="submit" icon="check" class="justify-center" wire:loading.attr="disabled" wire:target="save">
                {{ __('Create listing') }}
            </flux:button>
        </div>
    </form>
</section>
