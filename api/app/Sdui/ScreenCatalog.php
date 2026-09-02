<?php

namespace App\Sdui;

use App\Sdui\Screens\DetailsScreen;
use App\Sdui\Screens\FormScreen;
use App\Sdui\Screens\HomeScreen;
use JsonSerializable;
use InvalidArgumentException;

class ScreenCatalog
{
    /** @var array<string, class-string> */
    private array $screens = [
        'home' => HomeScreen::class,
        'details' => DetailsScreen::class,
        'form' => FormScreen::class,
    ];

    public function has(string $name): bool
    {
        return isset($this->screens[$name]);
    }

    /**
     * @return array<string, mixed>
     */
    public function build(string $name): array
    {
        $widget = $this->widget($name);

        return json_decode(json_encode($widget, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);
    }

    public function widget(string $name): JsonSerializable
    {
        $class = $this->screens[$name] ?? null;
        if ($class === null) {
            throw new InvalidArgumentException("Unknown screen [{$name}].");
        }

        return $class::build();
    }
}
