<?php

namespace App\Http\Requests\TypeAbonements;

use Illuminate\Foundation\Http\FormRequest;



class StoreTypeAbonementRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nom' => 'required|string|max:255|unique:type_abonements',
            'taux' => 'required|numeric|min:0',
            'prix' => 'required|numeric|min:0',
            'maxTicket' => 'required|integer|min:1'
        ];
    }
}
