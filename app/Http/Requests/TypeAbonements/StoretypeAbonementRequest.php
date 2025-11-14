<?php

namespace App\Http\Requests\TypeAbonements;

use Illuminate\Foundation\Http\FormRequest;

class StoreTypeAbonementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255|unique:type_abonements,nom',
            'libelle' => 'required|string|max:255',
            'type_compagnie' => 'required|in:privee,publique',
            'prix_mensuel' => 'required|numeric|min:0',
            'limite_notifications' => 'required|integer|min:-1',
            'acces_notes' => 'boolean',
            'commission_sur_place' => 'required|numeric|min:0',
            'commission_en_ligne' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'est_actif' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire',
            'nom.unique' => 'Ce nom d\'abonnement existe déjà',
            'libelle.required' => 'Le libellé est obligatoire',
            'type_compagnie.required' => 'Le type de compagnie est obligatoire',
            'prix_mensuel.required' => 'Le prix mensuel est obligatoire',
            'limite_notifications.required' => 'La limite de notifications est obligatoire',
            'commission_sur_place.required' => 'La commission sur place est obligatoire',
            'commission_en_ligne.required' => 'La commission en ligne est obligatoire',
        ];
    }

    public function attributes(): array
    {
        return [
            'nom' => 'nom',
            'libelle' => 'libellé',
            'type_compagnie' => 'type de compagnie',
            'prix_mensuel' => 'prix mensuel',
            'limite_notifications' => 'limite de notifications',
            'commission_sur_place' => 'commission sur place',
            'commission_en_ligne' => 'commission en ligne',
        ];
    }
}
