<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'client_name' => 'required|string|max:50',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|numeric|unique:users',
            'agency' => 'required|integer',
            'industry' => 'required|integer',
            'password' => 'required|string|confirmed|min:8|max:12|regex:/^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]+$/',
            'password_confirmation' => 'required|string',
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'password.regex' => 'Invalid Format. Password should be 8 characters, with at least 1 number and special characters.',
        ];
    }

    /**
     * Get the body parameters for Scribe documentation.
     *
     * @return array
     */
    public function bodyParameters()
    {
        return [
            'client_name' => [
                'description' => 'The name of the user',
            ],
            'email' => [
                'description' => 'The email of the user',
            ],
            'phone_number' => [
                'description' => 'The phone number of the user',
            ],
            'agency' => [
                'description' => 'The agency ID',
            ],
            'password' => [
                'description' => 'The password for the user account',
                'example' => 'Password123!',
            ],
            'password_confirmation' => [
                'description' => 'The password confirmation',
                'example' => 'Password123!',
            ],
        ];
    }
}
