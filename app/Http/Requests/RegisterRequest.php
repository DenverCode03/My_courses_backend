<?php

namespace App\Http\Requests;

use App\Resources\Enum\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class RegisterRequest extends FormRequest
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
            'name' => 'required|regex:/^[a-zA-Z0-9]{3,100}$/',
            'surname' => 'required|regex:/^[a-zA-Z0-9]{3,100}$/',
            'email' => 'required|email|unique:users',
            'role' => ['required', Rule::enum(UserRole::class)],
            'password' => 'required|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@^!%*?#&]).{8,}$/',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Le nom est requis',
            'role.required' => 'Le role est requis',
            'role.rule' => 'La valeur entrée du role est invalide',
            'name.regex' => 'Le nom doit comporter des caracteres alphabetiques',
            'surname.regex' => 'Le prénom doit comporter des caracteres alphabetiques',
            'surname.required' => 'Le prénom est requis',
            'email.required' => 'L\'email est requis',
            'email.email' => 'L\'email n\'est pas conforme',
            'email.unique' => 'L\'email doit etre unique',
            'password.required' => 'Le mot de passe est requis',
            'password.regex' => 'Le mot de passe doit avoir au moins une majuscule, un nombre et un caractere spéciale',
        ];
    }

      protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Erreur de validation',
            'errors' => $validator->errors()
        ], 422));
    }
}
