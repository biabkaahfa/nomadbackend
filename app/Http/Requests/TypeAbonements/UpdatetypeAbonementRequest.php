<?php

namespace App\Http\Requests\TypeAbonements;

//use App\Models\typeAbonement;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTypeAbonementRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
          $id = optional($this->typeAbonement)->id;
        return [


'nom' => 'required|string|max:255|unique:type_abonements,nom,' . $id,

            'taux' => 'required|numeric|min:0',
            'prix' => 'required|numeric|min:0',
            'maxTicket' => 'required|integer|min:1'
        ];
    }
}
