<?php

namespace App\Filament\Navigation;

use Closure;

class CustomNavigationItem
{
    public string $label;
    public ?string $url = null;
    public ?string $icon = null;
    public ?string $group = null;
    public array $subItems = [];
    public ?Closure $isActiveWhen = null;

    public function __construct(string $label, ?string $url = null, ?string $icon = null, ?string $group = null)
    {
        $this->label = $label;
        $this->url = $url;
        $this->icon = $icon;
        $this->group = $group;
    }

    public function subItems(array $items): static
    {
        $this->subItems = $items;
        return $this;
    }

    public function isActiveWhen(Closure $callback): static
    {
        $this->isActiveWhen = $callback;
        return $this;
    }

    public function isActive(): bool
    {
        if ($this->isActiveWhen instanceof Closure) {
            return (bool) ($this->isActiveWhen)();
        }

        // Check if current URL matches
        if ($this->url) {
            return request()->fullUrlIs($this->url . '*');
        }

        return false;
    }
}