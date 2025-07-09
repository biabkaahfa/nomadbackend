<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTrajetsRequest extends FormRequest
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
             'pointDepart' => ['required', 'string', 'max:255'],
        'pointArrive' => ['required', 'string', 'max:255'],
        'prix' => ['required', 'numeric', 'min:0'],
        'status' => ['required', 'in:ACTIF,INACTIF'],
        'distance' => ['required', 'numeric', 'min:0'],
        'idCompagnie' => ['required', 'exists:compagnies,id'],
        'idFrequence' => ['required', 'exists:frequence_trajets,id'],
            //
        ];
    }
}
