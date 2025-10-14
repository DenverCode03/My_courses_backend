<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
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
            'email' => 'required|email',
            'password' => 'required',
        ];
    }

    public function messages () {
        return [
            'email.required' => "L'email est requis",
            'password.required' => "Le mot de passe est requis",
            'email.email' => "L'email n'est pas conforme",
        ];
    }

    // protected function failedValidation(Validator $validator) {
    //     throw new HttpResponseException(response()->json([
    //         'status' => false,
    //         'message' => 'Erreur de validation',
    //         'errors' => $validator->errors()
    //     ], 422));
    // }
    
      protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Erreur de validation',
            'errors' => $validator->errors()
        ], 422));
    }
}
