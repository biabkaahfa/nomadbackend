<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorereservationsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'idVoyage' => 'required|exists:voyages,id',
            'nombrePlaces' => 'required|integer|min:1',
            'montantTotal' => 'required|numeric|min:1',
            'idVoyageRetour' => 'nullable|integer|exists:voyages,id',

            'passagers' => 'required|array|min:1',
            'passagers.*.name' => 'required|string|max:255',
            'passagers.*.telephone' => 'nullable|string|max:20',
            'passagers.*.email' => 'nullable|email',
            'passagers.*.namePersonneAPrevenir' => 'nullable|string|max:255',
            'passagers.*.numeroPersonneAPrevenir' => 'nullable|string|max:20',
            'passagers.*.emailPersonneAPrevenir' => 'nullable|email',
            'passagers.*.modeReception' => 'required|in:email,papier,application',
            'passagers.*.typeAchat' => 'required|in:En_ligne,sur_place',
        ];
    }

    public function messages(): array
    {
        return [
            'passagers.*.name.required' => 'Le nom du passager est requis.',
            'passagers.*.email.email' => 'Le mail du passager doit être valide.',
            'passagers.required' => 'Vous devez renseigner au moins un passager.',
        ];
    }
}
