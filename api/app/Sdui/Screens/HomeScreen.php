<?php

namespace App\Sdui\Screens;

use Sdui\Core\Action\SduiLogout;
use Sdui\Core\Action\SduiNavigate;
use Sdui\Core\Widget\AppBar;
use Sdui\Core\Widget\Column;
use Sdui\Core\Widget\FilledButton;
use Sdui\Core\Widget\Padding;
use Sdui\Core\Widget\Scaffold;
use Sdui\Core\Widget\SizedBox;
use Sdui\Core\Widget\Text;
use Sdui\Core\Widget\TextButton;

final class HomeScreen
{
    public static function build(): Scaffold
    {
        return Scaffold::make()
            ->appBar(AppBar::make()->title(Text::make('Home')))
            ->body(
                Padding::make(24)->child(
                    Column::make()
                        ->crossAxisAlignment('stretch')
                        ->children(
                            Text::make('Welcome')->style(['fontSize' => 24]),
                            SizedBox::make(height: 12),
                            Text::make('These screens are Stac JSON from the API.'),
                            SizedBox::make(height: 24),
                            FilledButton::make(Text::make('View details'), SduiNavigate::make('details')),
                            SizedBox::make(height: 12),
                            FilledButton::make(Text::make('Profile form'), SduiNavigate::make('form')),
                            SizedBox::make(height: 24),
                            TextButton::make(Text::make('Sign out'), SduiLogout::make()),
                        ),
                ),
            );
    }
}
