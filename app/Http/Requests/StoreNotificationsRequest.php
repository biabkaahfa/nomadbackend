<?php

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
    ];
    }
}
