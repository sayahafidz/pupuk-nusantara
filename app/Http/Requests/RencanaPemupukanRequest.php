<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RencanaPemupukanRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        $method = strtolower($this->method());
        $id = $this->route('id'); // Assuming the ID is passed in the route for PATCH

        $rules = [];
        switch ($method) {
            case 'post':
                $rules = [
                    'id_pupuk' => 'required|integer',
                    'regional' => 'required|string|max:255',
                    'kebun' => 'required|string|max:255',
                    'afdeling' => 'required|string|max:255',
                    'blok' => 'required|string|max:255',
                    'tahun_tanam' => 'required|integer',
                    'luas_blok' => 'required|numeric|min:0',
                    'jumlah_pokok' => 'required|integer|min:0',
                    'jenis_pupuk' => 'required|string|max:255',
                    'jumlah_pupuk' => 'required|integer|min:0',
                    'luas_pemupukan' => 'required|numeric|min:0',
                    'semester_pemupukan' => 'required|string|max:255',
                ];
                break;

            case 'patch':
                $rules = [
                    'id_pupuk' => 'sometimes|required|integer',
                    'regional' => 'sometimes|required|string|max:255',
                    'kebun' => 'sometimes|required|string|max:255',
                    'afdeling' => 'sometimes|required|string|max:255',
                    'blok' => 'sometimes|required|string|max:255',
                    'tahun_tanam' => 'sometimes|required|integer',
                    'luas_blok' => 'sometimes|required|numeric|min:0',
                    'jumlah_pokok' => 'sometimes|required|integer|min:0',
                    'jenis_pupuk' => 'sometimes|required|string|max:255',
                    'jumlah_pupuk' => 'sometimes|required|integer|min:0',
                    'luas_pemupukan' => 'sometimes|required|numeric|min:0',
                    'semester_pemupukan' => 'sometimes|required|string|max:255',
                ];
                break;
        }

        return $rules;
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'id_pupuk.required' => 'Id pupuk is required.',
            'regional.required' => 'Regional is required.',
            'kebun.required' => 'Kebun is required.',
            'afdeling.required' => 'Afdeling is required.',
            'blok.required' => 'Blok is required.',
            'tahun_tanam.required' => 'Tahun tanam is required.',
            'luas_blok.required' => 'Luas blok is required.',
            'jumlah_pokok.required' => 'Jumlah pokok is required.',
            'jenis_pupuk.required' => 'Jenis pupuk is required.',
            'jumlah_pupuk.required' => 'Jumlah pupuk is required.',
            'luas_pemupukan.required' => 'Luas pemupukan is required.',
            'semester_pemupukan.required' => 'Semester pemupukan is required.',
        ];
    }

    /**
     * handle failed validation
     * 
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        $data = [
            'status' => 'error',
            'message' => 'The given data was invalid.',
            'errors' => $validator->errors(),
        ];

        if ($this->expectsJson()) {
            throw new HttpResponseException(response()->json($data, 422));
        } else {
            throw new HttpResponseException(redirect()->back()->withInput()->with('errors', $validator->errors()));
        }
    }
}
