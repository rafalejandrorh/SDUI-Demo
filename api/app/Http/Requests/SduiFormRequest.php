<?php

namespace App\Http\Requests;

use App\Sdui\Validation\LaravelRulesToStacMapper;
use Illuminate\Foundation\Http\FormRequest;

abstract class SduiFormRequest extends FormRequest
{
    /**
     * @return list<array<string, mixed>>
     */
    public function stacRules(string $field): array
    {
        $rules = $this->rules()[$field] ?? [];

        return LaravelRulesToStacMapper::map($rules, $field, $this->messages());
    }
}
