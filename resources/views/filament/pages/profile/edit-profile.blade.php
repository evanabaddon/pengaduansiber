<x-filament-panels::page>
    <form wire:submit="save">
        <div class="max-w-2xl space-y-6">
            {{ $this->form }}
            
            <div class="mt-4">
                <x-filament::button type="submit">
                    Simpan
                </x-filament::button>
            </div>
        </div>
    </form>
</x-filament-panels::page> 