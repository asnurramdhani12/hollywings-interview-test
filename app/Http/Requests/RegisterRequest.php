<?php

namespace App\Http\Requests;

use App\Helpers\ResponseHelper;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            "name" => "required",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:6|max:12",
        ];
    }

    public function messages()
    {
        return [
            "name.required" => "Name is required",
            "email.required" => "Email is required",
            "email.email" => "Email is not valid",
            "email.unique" => "Email already exists",
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(ResponseHelper::error('Validation Error', 400, $validator->errors()));
    }
}
