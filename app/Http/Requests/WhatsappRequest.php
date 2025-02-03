<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class WhatsappRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        $method = strtolower($this->method());
        $whatsapp_id = $this->route('whatsapp');

        $rules = [];
        switch ($method) {
            case 'post':
                $rules = [
                    'name' => 'required|string|max:255',
                    'phone' => 'required|string|max:15|unique:whatsapp',
                    'status' => 'required|in:Active,Inactive',
                    'user_id' => 'required|exists:users,id',
                ];
                break;

            case 'patch':
                $rules = [
                    'name' => 'sometimes|required|string|max:255',
                    'phone' => 'sometimes|required|string|max:15|unique:whatsapp,phone,' . $whatsapp_id,
                    'status' => 'sometimes|required|in:Active,Inactive',
                    'user_id' => 'sometimes|required|exists:users,id',
                ];
                break;
        }

        return $rules;
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'phone.required' => 'The phone field is required.',
            'phone.max' => 'The phone number may not be greater than 15 characters.',
            'phone.unique' => 'The phone number has already been taken.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be either Active or Inactive.',
            'user_id.required' => 'The user field is required.',
            'user_id.exists' => 'The selected user is invalid.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        $data = [
            'status' => false,
            'message' => $validator->errors()->first(),
            'all_message' => $validator->errors()
        ];

        if ($this->ajax()) {
            throw new HttpResponseException(response()->json($data, 422));
        } else {
            throw new HttpResponseException(redirect()->back()->withInput()->with('errors', $validator->errors()));
        }
    }
}
