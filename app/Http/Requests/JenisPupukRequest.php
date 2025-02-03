<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class JenisPupukRequest extends FormRequest
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

        $rules = [];
        switch ($method) {
            case 'post':
                $rules = [
                    'nama_pupuk' => 'required|string|max:255',
                    'kode_pupuk' => 'required|string|max:255|unique:jenis_pupuk,kode_pupuk',
                    'jenis_pupuk' => 'required|string|max:255',
                    'harga' => 'required|numeric|min:0',
                    'stok' => 'required|integer|min:0',
                ];
                break;

            case 'patch':
                $id = $this->route('id'); // Assuming the ID is passed in the route for PATCH
                $rules = [
                    'nama_pupuk' => 'sometimes|required|string|max:255',
                    'kode_pupuk' => 'sometimes|required|string|max:255|unique:jenis_pupuk,kode_pupuk,' . $id,
                    'jenis_pupuk' => 'sometimes|required|string|max:255',
                    'harga' => 'sometimes|required|numeric|min:0',
                    'stok' => 'sometimes|required|integer|min:0',
                ];
                break;
        }

        return $rules;
    }

    /**
     * Custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'nama_pupuk.required' => 'Nama pupuk is required.',
            'kode_pupuk.required' => 'Kode pupuk is required.',
            'kode_pupuk.unique' => 'Kode pupuk must be unique.',
            'jenis_pupuk.required' => 'Jenis pupuk is required.',
            'harga.required' => 'Harga is required.',
            'stok.required' => 'Stok is required.',

        ];
    }

    /**
     * Handle failed validation.
     *
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        $data = [
            'status' => false,
            'message' => $validator->errors()->first(),
            'all_message' => $validator->errors(),
        ];

        if ($this->ajax()) {
            throw new HttpResponseException(response()->json($data, 422));
        } else {
            throw new HttpResponseException(redirect()->back()->withInput()->with('errors', $validator->errors()));
        }
    }
}
