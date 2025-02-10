<?php

namespace App\Http\Requests;

use App\Helpers\ResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BorrowingBooksRequest extends FormRequest
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
            'book_id' => 'required|exists:books,id|numeric',
            'quantities' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'User is required',
            'user_id.exists' => 'User not found',
            'user_id.numeric' => 'User must be a number',
            'book_id.required' => 'Book is required',
            'book_id.exists' => 'Book not found',
            'book_id.numeric' => 'Book must be a number',
            'borrow_date.required' => 'Borrow date is required',
            'borrow_date.date' => 'Borrow date must be a date',
            'return_date.required' => 'Return date is required',
            'return_date.date' => 'Return date must be a date',
            'quantities.required' => 'Quantities is required',
            'quantities.numeric' => 'Quantities must be a number',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(ResponseHelper::error('Validation Error', 400, $validator->errors()));
    }
}
