<?php

namespace Tests\Unit;

use App\Http\Requests\UpdateProfileRequest;
use App\Sdui\Validation\LaravelRulesToStacMapper;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use PHPUnit\Framework\TestCase;

class LaravelRulesToStacMapperTest extends TestCase
{
    public function test_maps_required_min_max_to_is_length(): void
    {
        $this->assertSame(
            [
                [
                    'rule' => 'isLength',
                    'options' => ['min' => 2, 'max' => 255],
                    'message' => 'Enter your name',
                ],
            ],
            LaravelRulesToStacMapper::map(
                ['required', 'string', 'min:2', 'max:255'],
                'name',
                ['name.min' => 'Enter your name'],
            ),
        );
    }

    public function test_maps_email_url_regex_and_confirmed(): void
    {
        $this->assertSame(
            [
                ['rule' => 'isLength', 'options' => ['min' => 1]],
                ['rule' => 'isEmail'],
            ],
            LaravelRulesToStacMapper::map(['required', 'email'], 'email'),
        );

        $this->assertSame(
            [['rule' => 'isURL']],
            LaravelRulesToStacMapper::map(['url'], 'website'),
        );

        $this->assertSame(
            [
                [
                    'rule' => 'matches',
                    'options' => ['pattern' => '(?i)^[a-z]+$'],
                ],
            ],
            LaravelRulesToStacMapper::map(['regex:/^[a-z]+$/i'], 'slug'),
        );

        $this->assertSame(
            [
                [
                    'rule' => 'compare',
                    'options' => ['fieldId' => 'password_confirmation'],
                ],
            ],
            LaravelRulesToStacMapper::map(['confirmed'], 'password'),
        );
    }

    public function test_maps_in_numeric_and_password_objects(): void
    {
        $this->assertSame(
            [
                [
                    'rule' => 'isIn',
                    'options' => ['values' => ['admin', 'user']],
                ],
            ],
            LaravelRulesToStacMapper::map([Rule::in(['admin', 'user'])], 'role'),
        );

        $this->assertSame(
            [['rule' => 'isNumeric'], ['rule' => 'isInt']],
            LaravelRulesToStacMapper::map(['numeric', 'integer'], 'age'),
        );

        $this->assertSame(
            [['rule' => 'isStrongPassword']],
            LaravelRulesToStacMapper::map([Password::default()], 'password'),
        );
    }

    public function test_pipe_string_and_unique_are_handled(): void
    {
        $this->assertSame(
            [
                ['rule' => 'isLength', 'options' => ['min' => 1]],
                ['rule' => 'isEmail'],
            ],
            LaravelRulesToStacMapper::map('required|email|unique:users,email', 'email'),
        );
    }

    public function test_profile_request_rules_drive_stac_output(): void
    {
        $request = new UpdateProfileRequest();

        $this->assertSame(
            [
                [
                    'rule' => 'isLength',
                    'options' => ['min' => 2, 'max' => 255],
                    'message' => 'Enter your name',
                ],
            ],
            $request->stacRules('name'),
        );

        $this->assertSame(
            [
                [
                    'rule' => 'isLength',
                    'options' => ['min' => 1, 'max' => 255],
                ],
                ['rule' => 'isEmail'],
            ],
            $request->stacRules('email'),
        );
    }
}
