<?php
// app/Http/Requests/UpdateNotificationsRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationsRequest extends FormRequest
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
            // ✅ AJOUT DES MODES D'ENVOI POUR LA MISE À JOUR
            'modes_envoi' => 'sometimes|array|min:1',
            'modes_envoi.*' => 'in:email,sms,push',
        ];
    }
}
