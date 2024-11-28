<x-filament-panels::page.simple>
    <div class="relative min-h-screen">
        {{-- Background image --}}
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/background.jpg') }}');">
            <div class="absolute inset-0 bg-black/50"></div>
        </div>

        <div class="relative flex items-center justify-center min-h-screen">
            <div class="w-full max-w-md">
                {{-- Logo dan Brand Name --}}
                <div class="text-center mb-6">
                    <img src="{{ asset('images/logo.png') }}" class="h-16 mx-auto" alt="Logo">
                    <h1 class="mt-3 text-2xl font-bold text-white">
                        {{ config('app.name') }}
                    </h1>
                    <p class="mt-2 text-white/80">
                        Sistem Pengaduan Siber
                    </p>
                </div>

                <x-filament-panels::card class="backdrop-blur-sm bg-white/80">
                    <div class="space-y-8">
                        <div class="text-center">
                            <h2 class="font-bold tracking-tight text-2xl">
                                {{ __('filament-panels::pages/auth/login.title') }}
                            </h2>
                            <p class="mt-2 text-gray-600">
                                {{ __('filament-panels::pages/auth/login.description') }}
                            </p>
                        </div>

                        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::auth.login.form.before') }}

                        <x-filament-panels::form wire:submit="authenticate">
                            {{ $this->form }}

                            <x-filament-panels::button type="submit" form="authenticate" class="w-full">
                                {{ __('filament-panels::pages/auth/login.buttons.submit.label') }}
                            </x-filament-panels::button>
                        </x-filament-panels::form>

                        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::auth.login.form.after') }}
                    </div>
                </x-filament-panels::card>
            </div>
        </div>
    </div>
</x-filament-panels::page.simple>

@push('styles')
<style>
    .fi-simple-page {
        background: transparent !important;
    }
</style>
@endpush