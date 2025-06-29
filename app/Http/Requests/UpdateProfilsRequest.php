<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class UpdateProfilsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
    //    $profilId = $this->route('profils');

    return [
        'name' => [
            'required',
            'string',
            'max:255',
            // Rule::unique('profils', 'name')->ignore($profilId),
        ],
        'description' => ['nullable', 'string', 'max:500'],
        'permissions' => ['nullable', 'array'],
        'permissions.*' => ['exists:permissions,id'],
    ];
    }
}
