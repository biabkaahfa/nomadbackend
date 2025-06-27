<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user') ? $this->route('user')->id : $this->user;
        
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'idProfil' => ['required', 'exists:profils,id'],
            'statut' => ['required', Rule::in(['actif', 'inactif'])],
            'telephone' => ['required', 'string', 'max:12', 'regex:/^[0-9+\-\s]+$/'],
            'idGarre' => ['nullable', 'exists:garres,id'],
            'idCompagnie' => ['nullable', 'exists:compagnies,id'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'idProfil.required' => 'Le profil est obligatoire.',
            'idProfil.exists' => 'Le profil sélectionné n\'existe pas.',
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut doit être actif ou inactif.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'telephone.regex' => 'Le format du téléphone n\'est pas valide.',
            'idGarre.exists' => 'La gare sélectionnée n\'existe pas.',
            'idCompagnie.exists' => 'La compagnie sélectionnée n\'existe pas.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être au format jpeg, png, jpg ou gif.',
            'image.max' => 'L\'image ne doit pas dépasser 2MB.',
        ];
    }
}