<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends StoreBookRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('book')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'designation' => ['required', 'string', 'max:255', Rule::unique('books', 'designation')->ignore($this->route('book'))],
        ];
    }
}
