<?php

namespace App\Sdui\Screens;

use Sdui\Core\Action\Navigate;
use Sdui\Core\Widget\AppBar;
use Sdui\Core\Widget\Center;
use Sdui\Core\Widget\Column;
use Sdui\Core\Widget\FilledButton;
use Sdui\Core\Widget\Icon;
use Sdui\Core\Widget\IconButton;
use Sdui\Core\Widget\Scaffold;
use Sdui\Core\Widget\SizedBox;
use Sdui\Core\Widget\Text;

final class DetailsScreen
{
    public static function build(): Scaffold
    {
        $back = Navigate::pop();

        return Scaffold::make()
            ->appBar(
                AppBar::make()
                    ->title(Text::make('Details'))
                    ->leading(IconButton::make(Icon::make('arrow_back'), $back)),
            )
            ->body(
                Center::make(
                    Column::make()
                        ->mainAxisSize('min')
                        ->children(
                            Text::make('This screen was opened from JSON.'),
                            SizedBox::make(height: 24),
                            FilledButton::make(Text::make('Back'), $back),
                        ),
                ),
            );
    }
}
