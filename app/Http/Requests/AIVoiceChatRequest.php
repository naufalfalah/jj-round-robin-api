<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AIVoiceChatRequest extends FormRequest
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
            'message' => 'required|string|max:1000',
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
            'message' => [
                'description' => 'The message for AI',
                'example' => 'What is programming?',
            ],
        ];
    }
}
