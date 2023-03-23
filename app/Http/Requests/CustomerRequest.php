<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
                    'alamat' => 'required|max:255',
                    'telepon' => 'numeric|digits_between:0,20|unique:master_customer,telepon'
                ];
        }
        if ($this->type == 'update') {
            $validate =
                [
                    'nama' => 'required|max:255',
                    'alamat' => 'required|max:255',
                    'telepon' => 'numeric|digits_between:0,20|unique:master_customer,telepon,' . $this->id
                ];
        }
        return $validate;
    }

    public function messages()
    {
        return [
            'nama.required' => 'Nama harus diisi',
            'nama.max' => 'Nama maksimal 255 karakter',
            'alamat.required' => 'Alamat harus diisi',
            'alamat.max' => 'Alamat maksimal 255 karakter',
            'telepon.required' => 'Telepon harus diisi',
            'telepon.unique' => 'Nomor telepon digunakan oleh customer lain',
            'telepon.max' => 'Telepon maksimal 20 digit',
        ];
    }
}
