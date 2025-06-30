<?php

namespace App\Http\Requests\Gare;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGarresRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255',
            'localisation' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('garres', 'localisation')->ignore($this->route('garre'))
            ],
            'ville' => 'required|string|max:255',
            'idCompagnie' => 'required|exists:compagnies,id'
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de la gare est obligatoire.',
            'name.string' => 'Le nom de la gare doit être une chaîne de caractères.',
            'name.max' => 'Le nom de la gare ne peut pas dépasser 255 caractères.',
            
            'localisation.string' => 'La localisation doit être une chaîne de caractères.',
            'localisation.max' => 'La localisation ne peut pas dépasser 255 caractères.',
            'localisation.unique' => 'Cette localisation existe déjà.',
            
            'ville.required' => 'La ville est obligatoire.',
            'ville.string' => 'La ville doit être une chaîne de caractères.',
            'ville.max' => 'La ville ne peut pas dépasser 255 caractères.',
            
            'idCompagnie.required' => 'La compagnie est obligatoire.',
            'idCompagnie.exists' => 'La compagnie sélectionnée n\'existe pas.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nom de la gare',
            'localisation' => 'localisation',
            'ville' => 'ville',
            'idCompagnie' => 'compagnie'
        ];
    }
}