<?php

namespace App\Http\Requests;

use App\Helpers\ResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class BookRequest extends FormRequest
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
        $titleRules = 'required|unique:books,title';
        $update = $this->route()->getActionMethod() === 'update';

        if ($update) {
            $titleRules = 'required';
        }

        return [
            'title' => $titleRules,
            'author' => 'required',
            'description' => 'required',
            'image' => 'required',
            'category_id' => 'required|numeric',
            'stock' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Title is required',
            'author.required' => 'Author is required',
            'description.required' => 'Description is required',
            'image.required' => 'Image is required',
            'category_id.required' => 'Category is required',
            'category_id.numeric' => 'Category must be a number',
            'stock.required' => 'Stock is required',
            'stock.numeric' => 'Stock must be a number',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        Log::error($validator->errors());
        throw new HttpResponseException(ResponseHelper::error('Validation Error', 400, $validator->errors()));
    }
}
