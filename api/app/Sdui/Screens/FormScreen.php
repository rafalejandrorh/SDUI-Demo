<?php

namespace App\Sdui\Screens;

use App\Http\Requests\UpdateProfileRequest;
use Sdui\Core\Action\GetFormValue;
use Sdui\Core\Action\Navigate;
use Sdui\Core\Action\NetworkRequest;
use Sdui\Core\Action\NetworkResult;
use Sdui\Core\Action\ShowDialog;
use Sdui\Core\Action\ShowSnackBar;
use Sdui\Core\Action\ValidateForm;
use Sdui\Core\Widget\AppBar;
use Sdui\Core\Widget\Column;
use Sdui\Core\Widget\FilledButton;
use Sdui\Core\Widget\Form;
use Sdui\Core\Widget\Icon;
use Sdui\Core\Widget\IconButton;
use Sdui\Core\Widget\Padding;
use Sdui\Core\Widget\Scaffold;
use Sdui\Core\Widget\SizedBox;
use Sdui\Core\Widget\Text;
use Sdui\Core\Widget\TextFormField;

final class FormScreen
{
    public static function build(): Scaffold
    {
        $rules = new UpdateProfileRequest();
        $back = Navigate::pop();

        $submit = NetworkRequest::make('/sdui/actions/profile')
            ->method('post')
            ->contentType('application/json')
            ->body([
                'name' => GetFormValue::make('name'),
                'email' => GetFormValue::make('email'),
            ])
            ->results([
                NetworkResult::make(200, ShowSnackBar::make(Text::make('Saved.'))),
                NetworkResult::make(422, ShowSnackBar::make(Text::make('Check the highlighted fields.'))),
                NetworkResult::make(500, ShowDialog::make(Text::make('Something went wrong.'))),
            ]);

        return Scaffold::make()
            ->appBar(
                AppBar::make()
                    ->title(Text::make('Profile'))
                    ->leading(IconButton::make(Icon::make('arrow_back'), $back)),
            )
            ->body(
                Padding::make(24)->child(
                    Form::make()
                        ->autovalidateMode('onUserInteraction')
                        ->child(
                            Column::make()
                                ->crossAxisAlignment('stretch')
                                ->children(
                                    TextFormField::make('name')
                                        ->decoration(['labelText' => 'Name'])
                                        ->validatorRules($rules->stacRules('name')),
                                    SizedBox::make(height: 16),
                                    TextFormField::make('email')
                                        ->keyboardType('emailAddress')
                                        ->decoration(['labelText' => 'Email'])
                                        ->validatorRules($rules->stacRules('email')),
                                    SizedBox::make(height: 24),
                                    FilledButton::make(
                                        Text::make('Submit'),
                                        ValidateForm::make(
                                            $submit,
                                            ShowDialog::make(Text::make('Check the highlighted fields.')),
                                        ),
                                    ),
                                ),
                        ),
                ),
            );
    }
}
