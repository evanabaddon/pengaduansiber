<?php

namespace App\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;

class MoneyDisplay extends Field
{
    protected string $view = 'components.money-display';

    public function prefix(string|Closure $value): static
    {
        return $this->extraAttributes(array_merge($this->getExtraAttributes(), [
            'prefix' => $value,
        ]));
    }

    public function suffix(string|Closure $value): static
    {
        return $this->extraAttributes(array_merge($this->getExtraAttributes(), [
            'suffix' => $value,
        ]));
    }
}