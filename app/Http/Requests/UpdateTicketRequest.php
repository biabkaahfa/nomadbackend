<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
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
            'modeAchat' => ['required', 'in:sur place'],
            'modeReception' => ['required', 'in:email,papier,application'],
            'name' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email'],
            'idVoyage' => ['required', 'exists:voyages,id'],
            'idPaiement' => ['required', 'exists:paiements,id'],
            'dateScan' => ['nullable', 'date'],
        ];
    }
}
