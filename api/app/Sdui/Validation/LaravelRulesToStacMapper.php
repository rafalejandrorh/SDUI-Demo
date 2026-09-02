<?php

namespace App\Sdui\Validation;

use Illuminate\Validation\Rules\Email;
use Illuminate\Validation\Rules\Password;
use Stringable;

final class LaravelRulesToStacMapper
{
    /**
     * @param  array<int, mixed>|string  $rules
     * @param  array<string, string>  $messages  Laravel-style keys (`field.rule` or `rule`)
     * @return list<array<string, mixed>>
     */
    public static function map(array|string $rules, string $field = '', array $messages = []): array
    {
        if (is_string($rules)) {
            $rules = $rules === '' ? [] : explode('|', $rules);
        }

        $stac = [];
        $min = null;
        $max = null;
        $lengthMessage = null;

        foreach ($rules as $rule) {
            if ($rule instanceof Email) {
                $stac[] = self::rule('isEmail', message: self::message($messages, $field, 'email'));
                continue;
            }

            if ($rule instanceof Password) {
                $stac[] = self::rule('isStrongPassword', message: self::message($messages, $field, 'password'));
                continue;
            }

            if (! is_string($rule)) {
                $rule = $rule instanceof Stringable ? (string) $rule : null;
            }

            if (! is_string($rule) || $rule === '') {
                continue;
            }

            $name = strtolower(explode(':', $rule, 2)[0]);
            $parameter = str_contains($rule, ':') ? substr($rule, strlen($name) + 1) : null;

            if ($name === 'required') {
                $min = max($min ?? 0, 1);
                $lengthMessage ??= self::message($messages, $field, 'required');
                continue;
            }

            if ($name === 'min') {
                $min = max($min ?? 0, (int) $parameter);
                $lengthMessage = self::message($messages, $field, 'min') ?? $lengthMessage;
                continue;
            }

            if ($name === 'max') {
                $max = (int) $parameter;
                $lengthMessage ??= self::message($messages, $field, 'max');
                continue;
            }

            $mapped = match ($name) {
                'email' => self::rule('isEmail', message: self::message($messages, $field, 'email')),
                'regex' => self::rule(
                    'matches',
                    ['pattern' => self::regexPattern((string) $parameter)],
                    self::message($messages, $field, 'regex'),
                ),
                'confirmed' => $field === '' ? null : self::rule(
                    'compare',
                    ['fieldId' => $field.'_confirmation'],
                    self::message($messages, $field, 'confirmed'),
                ),
                'numeric' => self::rule('isNumeric', message: self::message($messages, $field, 'numeric')),
                'integer' => self::rule('isInt', message: self::message($messages, $field, 'integer')),
                'url' => self::rule('isURL', message: self::message($messages, $field, 'url')),
                'uuid' => self::rule('isUUID', message: self::message($messages, $field, 'uuid')),
                'in' => self::rule(
                    'isIn',
                    ['values' => self::csvValues((string) $parameter)],
                    self::message($messages, $field, 'in'),
                ),
                default => null,
            };

            if ($mapped !== null) {
                $stac[] = $mapped;
            }
        }

        if ($min !== null || $max !== null) {
            $options = array_filter(
                ['min' => $min, 'max' => $max],
                static fn (mixed $value): bool => $value !== null,
            );
            array_unshift($stac, self::rule('isLength', $options, $lengthMessage));
        }

        return $stac;
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    private static function rule(string $name, array $options = [], ?string $message = null): array
    {
        $payload = ['rule' => $name];

        if ($options !== []) {
            $payload['options'] = $options;
        }

        if ($message !== null && $message !== '') {
            $payload['message'] = $message;
        }

        return $payload;
    }

    /** @param  array<string, string>  $messages */
    private static function message(array $messages, string $field, string $rule): ?string
    {
        if ($field !== '' && isset($messages[$field.'.'.$rule])) {
            return $messages[$field.'.'.$rule];
        }

        return $messages[$rule] ?? null;
    }

    private static function regexPattern(string $pattern): string
    {
        if (preg_match('#^/(.*)/([a-zA-Z]*)$#s', $pattern, $matches) === 1) {
            $body = $matches[1];
            $flags = $matches[2];

            return $flags !== '' ? '(?'.$flags.')'.$body : $body;
        }

        return $pattern;
    }

    /** @return list<string> */
    private static function csvValues(string $parameter): array
    {
        if ($parameter === '') {
            return [];
        }

        return array_values(array_filter(
            array_map(
                static fn (string $value): string => trim($value, " \t\"'"),
                explode(',', $parameter),
            ),
            static fn (string $value): bool => $value !== '',
        ));
    }
}
