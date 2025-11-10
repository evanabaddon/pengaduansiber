@php
    use Filament\Facades\Filament;
    use Filament\Navigation\NavigationGroup;

    $groups = Filament::getNavigation();
@endphp

<div class="mt-4">
    @foreach ($groups as $group)
        @if ($group instanceof NavigationGroup)
            <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-2">
                {{ $group->getLabel() }}
            </p>
            @foreach ($group->getItems() as $item)
                <x-filament::layouts.app.sidebar-item :item="$item" />
            @endforeach
        @endif
    @endforeach
</div>
