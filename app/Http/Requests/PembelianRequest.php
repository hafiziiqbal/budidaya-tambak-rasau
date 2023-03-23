<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PembelianRequest extends FormRequest
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
            'tanggal_beli' => 'required|date_format:d-m-Y',
            'supplier' => 'required|exists:supplier,id',
            'total_bruto' => 'required|numeric|digits_between:0,20',
            'potongan_harga' => 'required|numeric|digits_between:0,20',
            'total_netto' => 'required|numeric|digits_between:0,20',
            'detail_beli.*.id_produk' => 'required|exists:produk,id',
            'detail_beli.*.harga_satuan' => 'required|numeric|digits_between:0,20',
            'detail_beli.*.quantity' => 'required|numeric|digits_between:0,8',
            'detail_beli.*.diskon_persen' => 'required|numeric|digits_between:0,8',
            'detail_beli.*.diskon_rupiah' => 'required|numeric|digits_between:0,20',
            'detail_beli.*.subtotal' => 'required|numeric|digits_between:0,20',
        ];
    }

    public function messages()
    {
        return [
            'detail_beli.*.subtotal.digits_between' => 'Subtotal minimal 0 digit dan maksimal 20 digit',
            'detail_beli.*.subtotal.numeric' => 'Subtotal harus berupa angka',
            'detail_beli.*.subtotal.required' => 'Subtotal harus diisi',

            'detail_beli.*.diskon_rupiah.digits_between' => 'Diskon rupiah minimal 0 digit dan maksimal 20 digit',
            'detail_beli.*.diskon_rupiah.numeric' => 'Diskon rupiah harus berupa angka',
            'detail_beli.*.diskon_rupiah.required' => 'Diskon rupiah harus diisi',

            'detail_beli.*.diskon_persen.digits_between' => 'Diskon persen minimal 0 digit dan maksimal 8 digit',
            'detail_beli.*.diskon_persen.numeric' => 'Diskon persen harus berupa angka',
            'detail_beli.*.diskon_persen.required' => 'Diskon persen harus diisi',

            'detail_beli.*.quantity.digits_between' => 'Quantity minimal 0 digit dan maksimal 8 digit',
            'detail_beli.*.quantity.numeric' => 'Quantity harus berupa angka',
            'detail_beli.*.quantity.required' => 'Quantity harus diisi',

            'detail_beli.*.harga_satuan.digits_between' => 'Harga satuan minimal 0 digit dan maksimal 20 digit',
            'detail_beli.*.harga_satuan.numeric' => 'Harga satuan harus berupa angka',
            'detail_beli.*.harga_satuan.required' => 'Harga satuan harus diisi',

            'detail_beli.*.id_produk.required' => 'Produk harus dipilih',
            'detail_beli.*.id_produk.exists' => 'Produk tidak tersedia',

            'total_netto.digits_between' => 'Total netto minimal 0 digit dan maksimal 20 digit',
            'total_netto.numeric' => 'Total netto harus berupa angka',
            'total_netto.required' => 'Total netto harus diisi',

            'potongan_harga.digits_between' => 'Potongan harga minimal 0 digit dan maksimal 20 digit',
            'potongan_harga.numeric' => 'Potongan harga harus berupa angka',
            'potongan_harga.required' => 'Potongan harga harus diisi',

            'total_bruto.digits_between' => 'Total bruto minimal 0 digit dan maksimal 20 digit',
            'total_bruto.numeric' => 'Total bruto harus berupa angka',
            'total_bruto.required' => 'Total bruto harus diisi',

            'supplier.exists' => 'Supplier tidak tersedia',
            'supplier.required' => 'Supplier harus dipilih',

            'tanggal_beli.required' => 'Tanggal beli harus diisi',
            'tanggal_beli.date_format' => 'Tanggal beli memiliki format d-m-Y',
        ];
    }
}
