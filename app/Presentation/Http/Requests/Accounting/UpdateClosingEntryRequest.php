<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClosingEntryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return ['name' => ['sometimes', 'required', 'string', 'max:255']];
    }

    public function messages(): array
    {
        return ['name.required' => 'Name is required.'];
    }
}
