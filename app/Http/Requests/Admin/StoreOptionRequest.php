<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreOptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body'       => ['nullable', 'string', 'max:1000'],
            'is_correct' => ['nullable', 'boolean'],
            'image'      => ['nullable', 'image', 'max:5120'],
        ];
    }
}
