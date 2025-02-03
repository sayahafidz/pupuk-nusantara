<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SettingRequest extends FormRequest
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
        $id = $this->route('id');

        $rules = [];
        switch ($method) {
            case 'post':
                $rules = [
                    'name' => 'required|string|max:255',
                    'value' => 'required|string|max:255',
                ];
                break;

            case 'patch':
                $rules = [
                    'name' => 'sometimes|required|string|max:255',
                    'value' => 'sometimes|required|string|max:255',
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
            'name.required' => 'Nama harus diisi',
            'name.string' => 'Nama harus berupa string',
            'name.max' => 'Nama maksimal 255 karakter',
            'value.required' => 'Nilai harus diisi',
            'value.string' => 'Nilai harus berupa string',
            'value.max' => 'Nilai maksimal 255 karakter',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
