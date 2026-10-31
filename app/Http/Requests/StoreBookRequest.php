<?php

namespace App\Http\Requests;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Book::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'designation' => ['required', 'string', 'max:255', Rule::unique('books', 'designation')],
            'auteur' => ['required', 'string', 'max:255'],
            'prix' => ['required', 'numeric', 'gt:0', 'max:99999999.99'],
            'type_id' => ['required', 'integer', 'exists:types,id'],
            'annee' => ['nullable', 'date'],
            'editeur' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'cover' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['integer', 'distinct', 'exists:categories,id'],
            'language_id' => ['nullable', 'integer', 'exists:languages,id'],
        ];
    }
}
