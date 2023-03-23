<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PembagianBibitRequest extends FormRequest
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
        print_r($this->all());
        exit();
        $validate = '';
        if ($this->type == 'store-all') {
            $validate =
                [
                    'tanggal_beli' => 'required|date_format:d-m-Y',
                    'supplier' => 'required|exists:supplier,id',
                    'total_bruto' => 'required|numeric|digits_between:0,20',
                    'potongan_harga' => 'required|numeric|digits_between:0,20',
                    'total_netto' => 'required|numeric|digits_between:0,20',
                    'detail_beli.*.id_produk' => 'required|exists:produk,id',
                    'detail_beli.*.harga_satuan' => 'required|numeric|digits_between:0,20',
                    'detail_beli.*.quantity' => 'required|numeric|between:0,99999999.99',
                    'detail_beli.*.diskon_persen' => 'required|numeric|between:0,99999999.99',
                    'detail_beli.*.diskon_rupiah' => 'required|numeric|digits_between:0,20',
                    'detail_beli.*.subtotal' => 'required|numeric|digits_between:0,20',
                ];
        }
        return $validate;
    }
}
