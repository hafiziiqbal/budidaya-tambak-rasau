<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JaringRequest extends FormRequest
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
        $validate = '';
        if ($this->type == 'store') {
            $validate =
                [
                    'nama' => 'required|max:255',
                    'posisi' => 'required|max:255',
                ];
        }
        if ($this->type == 'update') {
            $validate =
                [
                    'nama' => 'required|max:255',
                    'id_kolam' => 'nullable',
                    'posisi' => 'required|max:255',
                ];
        }
        return $validate;
    }
    public function messages()
    {
        return [
            'id_kolam.required' => 'Kolam produk harus diisi',
            'id_kolam.exists' => 'Kolam produk tidak tersedia',
            'nama.required' => 'Nama harus diisi',
            'nama.max' => 'Nama maksimal 255 karakter',
            'posisi.required' => 'Posisi harus diisi',
            'posisi.max' => 'Posisi maksimal 255 karakter',
        ];
    }
}
