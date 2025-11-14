<?php

namespace App\Http\Requests\Gare;

use Illuminate\Foundation\Http\FormRequest;

class StoreGarresRequest extends FormRequest
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
                'required',
                'string',
                'max:255',
                'unique:garres,localisation',
                'regex:/^-?\d{1,2}\.\d+,\s*-?\d{1,3}\.\d+$/'
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

            'localisation.required' => 'Les coordonnées GPS sont obligatoires. Veuillez sélectionner une position sur la carte.',
            'localisation.string' => 'Les coordonnées GPS doivent être une chaîne de caractères.',
            'localisation.max' => 'Les coordonnées GPS ne peuvent pas dépasser 255 caractères.',
            'localisation.unique' => 'Cette localisation existe déjà.',
            'localisation.regex' => 'Le format des coordonnées GPS est invalide. Utilisez le format: latitude, longitude (ex: 7.539989, -5.547080)',

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
            'localisation' => 'coordonnées GPS',
            'ville' => 'ville',
            'idCompagnie' => 'compagnie'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Nettoyer les espaces autour des coordonnées
        if ($this->has('localisation')) {
            $this->merge([
                'localisation' => trim($this->localisation)
            ]);
        }
    }
}
