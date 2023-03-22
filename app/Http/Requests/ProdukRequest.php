<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProdukRequest extends FormRequest
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
            'id_kategori' => 'required|exists:kategori,id',
            'nama' => 'required|max:255',
            'quantity' => 'required|numeric|digits_between:0,8',
        ];
    }

    public function messages()
    {
        return [
            'id_kategori.required' => 'Kategori produk harus diisi',
            'id_kategori.exists' => 'Kategori produk tidak tersedia',
            'nama.required' => 'Nama harus diisi',
            'nama.max' => 'Nama maksimal 255 karakter',
            'quantity.required' => 'Quantity harus diisi',
            'quantity.numeric' => 'Quantity tidak harus berupa angka',
            'quantity.digits_between' => 'Quantity minimal 0 digit & maximal 8 digit',
        ];
    }
}
