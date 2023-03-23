<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KolamRequest extends FormRequest
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
            'nama' => 'required|max:255',
            'posisi' => 'required|max:255',
        ];
    }

    public function messages()
    {
        return [
            'nama.required' => 'Nama harus diisi',
            'nama.max' => 'Nama maksimal 255 karakter',
            'posisi.required' => 'Posisi harus diisi',
            'posisi.max' => 'Posisi maksimal 255 karakter',
        ];
    }
}
