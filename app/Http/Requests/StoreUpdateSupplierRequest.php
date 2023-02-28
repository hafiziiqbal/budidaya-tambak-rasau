<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateSupplierRequest extends FormRequest
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

        if ($this->id == '') {
            $unique = 'unique:supplier,telepon';
        } else {
            $unique = '';
        }

        return [
            'nama' => 'required',
            'alamat' => 'required',
            'telepon' => 'numeric|' . $unique
        ];
    }

    public function messages()
    {
        return [
            'nama.required' => 'Nama wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'telepon.required' => 'Telepon wajib diisi',
            'telepon.unique' => 'Nomor Telepon Digunakan Oleh Supplier Lain',
        ];
    }
}
