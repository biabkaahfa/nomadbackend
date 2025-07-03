<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
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
            'dateReservation' => ['required', 'date'],
            'statut' => ['required', 'in:CONFIRME,ANNULE,REPORTE,UTILISE'],
            'typeAchat' => ['nullable', 'in:sur_place'],
            'modeReception' => ['required', 'in:email,papier,application'],
            'name' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email'],
            'idVoyage' => ['required', 'exists:voyages,id'],
            'idPaiement' => ['nullable', 'exists:paiements,id'],
            'dateScan' => ['nullable', 'date'],
            'namePersonneAPrevenir' => 'required|string|max:255',
'numeroPersonneAPrevenir' => 'required|string|max:20',
'emailPersonneAPrevenir' => 'nullable|email',

        ];
    }
}
