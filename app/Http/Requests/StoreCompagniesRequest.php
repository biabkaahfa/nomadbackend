<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompagniesRequest extends FormRequest
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
            'name'=>['string','required','max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
             'type' => ['required', 'in:PUBLIC,PRIVE'],
            'description'=>['string','nullable','max:500'],
            'telephone' => ['required', 'string', 'max:12', 'regex:/^[0-9+\-\s]+$/'],
            'image'=>['image','nullable','mimes:png,jpg,jpeg','max:2048'],
            
           

            //
        ];
    }
}
