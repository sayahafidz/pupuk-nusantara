<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class MasterDataRequest extends FormRequest
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
        $id = $this->route('id'); // Assuming the ID is passed in the route for PATCH

        $rules = [];
        switch ($method) {
            case 'post':
                $rules = [
                    'kondisi' => 'required|string|max:255',
                    'status_umur' => 'required|string|max:255',
                    'kode_kebun' => 'required|string|max:255',
                    'nama_kebun' => 'required|string|max:255',
                    'kkl_kebun' => 'nullable|string|max:255',
                    'afdeling' => 'required|string|max:255',
                    'tahun_tanam' => 'required|integer',
                    'no_blok' => 'required|string|max:255',
                    'luas' => 'required|numeric|min:0',
                    'jlh_pokok' => 'required|integer|min:0',
                    'pkk_ha' => 'required|numeric|min:0',
                ];
                break;

            case 'patch':
                $rules = [
                    'kondisi' => 'sometimes|required|string|max:255',
                    'status_umur' => 'sometimes|required|string|max:255',
                    'kode_kebun' => 'sometimes|required|string|max:255',
                    'nama_kebun' => 'sometimes|required|string|max:255',
                    'kkl_kebun' => 'nullable|string|max:255',
                    'afdeling' => 'sometimes|required|string|max:255',
                    'tahun_tanam' => 'sometimes|required|integer',
                    'no_blok' => 'sometimes|required|string|max:255',
                    'luas' => 'sometimes|required|numeric|min:0',
                    'jlh_pokok' => 'sometimes|required|integer|min:0',
                    'pkk_ha' => 'sometimes|required|numeric|min:0',
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
            'kondisi.required' => 'Kondisi is required.',
            'status_umur.required' => 'Status umur is required.',
            'kode_kebun.required' => 'Kode kebun is required.',
            'nama_kebun.required' => 'Nama kebun is required.',
            'afdeling.required' => 'Afdeling is required.',
            'tahun_tanam.required' => 'Tahun tanam is required.',
            'no_blok.required' => 'No blok is required.',
            'luas.required' => 'Luas is required.',
            'jlh_pokok.required' => 'Jumlah pokok is required.',
            'pkk_ha.required' => 'PKK/Ha is required.',
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
