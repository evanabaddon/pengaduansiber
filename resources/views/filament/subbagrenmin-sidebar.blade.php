<div class="fi-sidebar-nav-groups -mx-2 flex flex-col gap-y-7">
    @php
        $currentMenu = request('menu');
        $currentPath = request()->path();
    @endphp

    {{-- Urren --}}
    <div class="fi-sidebar-group">
        <div class="fi-sidebar-group-label">Urren</div>

        {{-- Persuratan --}}
        <x-filament::dropdown placement="bottom-start">
            <x-slot name="trigger">
                <button type="button" class="fi-sidebar-group-button w-full flex items-center gap-x-3">
                    Persuratan
                    <x-filament::icon icon="heroicon-m-chevron-down" class="w-5 h-5 ms-auto"/>
                </button>
            </x-slot>
            <x-filament::dropdown.list>
                <x-filament::dropdown.list.item href="{{ url('/subbagrenmin/surats/naskah-dinas?menu=urren') }}">
                    Naskah Dinas
                </x-filament::dropdown.list.item>
                <x-filament::dropdown.list.item href="{{ url('/subbagrenmin/surats/minlidik-sidik?menu=urren') }}">
                    Minlidik Sidik
                </x-filament::dropdown.list.item>
            </x-filament::dropdown.list>
        </x-filament::dropdown>
    </div>

    {{-- Urmintu --}}
    <div class="fi-sidebar-group">
        <div class="fi-sidebar-group-label">Urmintu</div>
        <x-filament::dropdown placement="bottom-start">
            <x-slot name="trigger">
                <button type="button" class="fi-sidebar-group-button w-full flex items-center gap-x-3">
                    Persuratan
                    <x-filament::icon icon="heroicon-m-chevron-down" class="w-5 h-5 ms-auto"/>
                </button>
            </x-slot>
            <x-filament::dropdown.list>
                <x-filament::dropdown.list.item href="{{ url('/subbagrenmin/surats/naskah-dinas?menu=urmintu') }}">
                    Naskah Dinas
                </x-filament::dropdown.list.item>
                <x-filament::dropdown.list.item href="{{ url('/subbagrenmin/surats/minlidik-sidik?menu=urmintu') }}">
                    Minlidik Sidik
                </x-filament::dropdown.list.item>
            </x-filament::dropdown.list>
        </x-filament::dropdown>

        {{-- Personel --}}
        <x-filament::dropdown.list.item href="{{ url('/subbagrenmin/personel') }}">
            Personel
        </x-filament::dropdown.list.item>
    </div>

    {{-- Urkeu --}}
    <div class="fi-sidebar-group">
        <div class="fi-sidebar-group-label">Urkeu</div>
        <x-filament::dropdown.list.item href="{{ url('/subbagrenmin/anggaran') }}">
            Anggaran
        </x-filament::dropdown.list.item>

        <x-filament::dropdown placement="bottom-start">
            <x-slot name="trigger">
                <button type="button" class="fi-sidebar-group-button w-full flex items-center gap-x-3">
                    Persuratan
                    <x-filament::icon icon="heroicon-m-chevron-down" class="w-5 h-5 ms-auto"/>
                </button>
            </x-slot>
            <x-filament::dropdown.list>
                <x-filament::dropdown.list.item href="{{ url('/subbagrenmin/surats/naskah-dinas?menu=urkeu') }}">
                    Naskah Dinas
                </x-filament::dropdown.list.item>
                <x-filament::dropdown.list.item href="{{ url('/subbagrenmin/surats/minlidik-sidik?menu=urkeu') }}">
                    Minlidik Sidik
                </x-filament::dropdown.list.item>
            </x-filament::dropdown.list>
        </x-filament::dropdown>
    </div>
</div>
