<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBusRequest extends FormRequest
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
            'numeroBus' => ['required', 'integer'],
            'nombrePlaces' => ['required', 'integer', 'min:1'],
            'nombrePlaceDispo' => ['required', 'integer', 'min:0', 'lte:nombrePlaces'],
            'idCompagnie' => ['required', 'exists:compagnies,id'],
            'status' => ['required', 'in:Actif,Inactif'],
        ];
    }
}
