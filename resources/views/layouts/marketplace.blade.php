<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
        <div class="marketplace-theme min-h-screen">
            <header class="border-b border-slate-200 bg-white/90 backdrop-blur-sm">
                <div class="flex items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                    <a href="{{ route('home') }}" class="text-xl font-semibold tracking-tight text-slate-900" wire:navigate>
                        {{ __('Kinkoza Marketplace') }}
                    </a>

                    <nav class="hidden items-center gap-8 text-sm font-medium text-slate-700 md:flex">
                        <a href="{{ route('home') }}" class="transition hover:text-blue-600" wire:navigate>{{ __('Browse') }}</a>
                        <a href="#" class="transition hover:text-blue-600">{{ __('Categories') }}</a>
                        <a href="#" class="transition hover:text-blue-600">{{ __('Suppliers') }}</a>
                    </nav>

                    <div class="hidden items-center gap-2 md:flex">
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-blue-200 hover:text-blue-600" wire:navigate>
                                {{ __('Dashboard') }}
                            </a>
                            <div>
                                <x-desktop-user-menu />
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-blue-200 hover:text-blue-600" wire:navigate>
                                {{ __('Log in') }}
                            </a>
                            <a href="{{ route('register') }}" class="inline-flex items-center rounded-full bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-blue-500" wire:navigate>
                                {{ __('Create account') }}
                            </a>
                        @endauth
                    </div>

                    <details class="relative md:hidden">
                        <summary class="flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 transition hover:border-blue-200 hover:text-blue-600 [&::-webkit-details-marker]:hidden" aria-label="{{ __('Open navigation menu') }}">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M4 7h16M4 12h16M4 17h16"></path>
                            </svg>
                        </summary>

                        <div class="absolute right-0 z-30 mt-3 w-56 overflow-hidden rounded-xl border border-slate-200 bg-white p-2 shadow-lg">
                            <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-blue-50 hover:text-blue-700" wire:navigate>
                                {{ __('Browse listings') }}
                            </a>

                            @auth
                                <a href="{{ route('dashboard') }}" class="mt-1 block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-blue-50 hover:text-blue-700" wire:navigate>
                                    {{ __('Dashboard') }}
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="mt-1 block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-blue-50 hover:text-blue-700" wire:navigate>
                                    {{ __('Log in') }}
                                </a>
                                <a href="{{ route('register') }}" class="mt-1 block rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-blue-500" wire:navigate>
                                    {{ __('Create account') }}
                                </a>
                            @endauth
                        </div>
                    </details>
                </div>
            </header>

            {{ $slot }}

            <footer class="bg-linear-to-r from-blue-600 via-blue-600 to-blue-500 text-white">
                <div class="grid gap-8 px-4 py-10 sm:px-6 md:grid-cols-[1.5fr_1fr_1fr] lg:px-8">
                    <div>
                        <p class="text-lg font-semibold tracking-tight">{{ __('Kinkoza Marketplace') }}</p>
                        <p class="mt-3 max-w-sm text-sm leading-6 text-blue-100">
                            {{ __('A trusted marketplace for sourcing commercial assets from verified businesses.') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold">{{ __('Marketplace') }}</p>
                        <div class="mt-3 flex flex-col items-start gap-2 text-sm text-blue-100">
                            <a href="{{ route('home') }}#marketplace-listings" class="transition hover:text-white">{{ __('Browse listings') }}</a>
                            <a href="{{ route('register') }}" class="transition hover:text-white" wire:navigate>{{ __('Sell on Kinkoza') }}</a>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-semibold">{{ __('Company') }}</p>
                        <div class="mt-3 flex flex-col items-start gap-2 text-sm text-blue-100">
                            <a href="mailto:hello@kinkoza.com" class="transition hover:text-white">{{ __('Contact us') }}</a>
                            <a href="mailto:careers@kinkoza.com" class="transition hover:text-white">{{ __('Careers') }}</a>
                        </div>
                    </div>
                </div>

                <div class="border-t border-white/20 px-4 py-4 text-sm text-blue-100 sm:px-6 lg:px-8">
                    &copy; {{ now()->year }} {{ __('Kinkoza') }}. {{ __('Built for commercial trade') }}.
                </div>
            </footer>

            @persist('toast')
                <flux:toast.group>
                    <flux:toast />
                </flux:toast.group>
            @endpersist

            @fluxScripts
        </div>
    </body>
</html>
