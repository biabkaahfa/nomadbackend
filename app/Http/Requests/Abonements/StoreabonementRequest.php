<?php

namespace App\Http\Requests\Abonements;

use Illuminate\Foundation\Http\FormRequest;


class StoreAbonementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Changé à true pour autoriser la requête
    }

    public function rules(): array
    {
        return [
            'idTypeAbonement' => 'required|exists:type_abonements,id',
            'idCompagnie' => 'sometimes|required|exists:compagnies,id',
            'duree' => 'nullable|integer|min:1'

        ];
    }

    public function messages()
    {
        return [
            'idTypeAbonement.required' => 'Le type d\'abonnement est obligatoire',
            'idTypeAbonement.exists' => 'Le type d\'abonnement sélectionné est invalide',
            'idCompagnie.required' => 'La compagnie est obligatoire',
            'idCompagnie.exists' => 'La compagnie sélectionnée est invalide',
            'duree.integer' => 'La durée doit être un nombre entier',
            'duree.min' => 'La durée doit être d\'au moins 1 jour'
        ];
    }
}
