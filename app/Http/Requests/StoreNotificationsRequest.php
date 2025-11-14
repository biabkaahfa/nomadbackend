<?php
// app/Http/Requests/StoreNotificationsRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationsRequest extends FormRequest
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
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'type' => 'required|in:retard,annulation,report,accident,rappel',
            'idVoyage' => 'required|exists:voyages,id',
            // ✅ AJOUT DES MODES D'ENVOI
            'modes_envoi' => 'required|array|min:1',
            'modes_envoi.*' => 'in:email,sms,push',
        ];
    }

    /**
     * Messages de validation personnalisés
     */
    public function messages(): array
    {
        return [
            'modes_envoi.required' => 'Veuillez sélectionner au moins un mode d\'envoi.',
            'modes_envoi.min' => 'Veuillez sélectionner au moins un mode d\'envoi.',
        ];
    }
}
