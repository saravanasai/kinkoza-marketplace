@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand  {{ $attributes }}>
        <x-slot name="logo" class="flex items-center">
            <x-app-logo-icon class="h-8 w-auto" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand  {{ $attributes }}>
        <x-slot name="logo" class="flex items-center">
            <x-app-logo-icon class="h-8 w-auto" />
        </x-slot>
    </flux:brand>
@endif
