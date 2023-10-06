<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PenjualanRequest extends FormRequest
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
        if ($this->type == 'store-all') {
            $validate =
                [
                    'customer' => 'required|exists:master_customer,id',
                    'total_bruto' => 'required|numeric|digits_between:1,20',
                    'potongan_harga' => 'required|numeric|digits_between:0,20',
                    'total_netto' => 'required|numeric|digits_between:1,20',
                    'pay' => 'required|numeric|digits_between:1,20',
                    'change' => 'required|numeric|digits_between:0,20',

                    'detail.*.id_detail_panen' => 'required|exists:detail_panen,id',
                    'detail.*.harga_satuan' => 'required|numeric|digits_between:1,20',                    'detail.*.quantity' => 'required|numeric|between:1,99999999.99',
                    'detail.*.diskon' => 'required|numeric|between:0,99999999.99',
                    'detail.*.subtotal' => 'required|numeric|digits_between:1,20',

                ];
        }

        if ($this->type == 'update-header') {
            $validate =
                [
                    'customer' => 'required|exists:master_customer,id',
                    'total_bruto' => 'required|numeric|digits_between:1,20',
                    'potongan_harga' => 'required|numeric|digits_between:0,20',
                    'total_netto' => 'required|numeric|digits_between:1,20',
                    'pay' => 'required|numeric|digits_between:1,20',
                    'change' => 'required|numeric|digits_between:0,20',
                ];
        }

        if ($this->type == 'update-detail') {
            $validate =
                [
                    'id_detail_panen' => 'required|exists:detail_panen,id',
                    'harga_satuan' => 'required|numeric|digits_between:1,20',
                    'quantity' => 'required|numeric|between:1,99999999.99',
                    'diskon' => 'required|numeric|between:0,99999999.99',
                    'subtotal' => 'required|numeric|digits_between:1,20',
                ];
        }

        if ($this->type == 'store-detail') {
            $validate =
                [
                    'id_detail_panen' => 'required',
                    'harga_satuan' => 'required|numeric|digits_between:1,20',
                    'quantity' => 'required|numeric|between:1,99999999.99',
                    'diskon' => 'required|numeric|between:0,99999999.99',
                    'subtotal' => 'nullable',
                ];
        }
        return $validate;
    }

    public function messages()
    {
        return [
            'subtotal.digits_between' => 'Subtotal minimal 1 digit dan maksimal 20 digit',
            'subtotal.numeric' => 'Subtotal harus berupa angka',
            'subtotal.required' => 'Subtotal harus diisi',

            'diskon.between' => 'Diskon minimal 1 digit dan maksimal 8 digit',
            'diskon.numeric' => 'Diskon harus berupa angka',
            'diskon.required' => 'Diskon harus diisi',

            'quantity.between' => 'Quantity minimal 1 digit dan maksimal 8 digit',
            'quantity.numeric' => 'Quantity harus berupa angka',
            'quantity.required' => 'Quantity harus diisi',

            'harga_satuan.digits_between' => 'Harga satuan minimal 1 digit dan maksimal 20 digit',
            'harga_satuan.numeric' => 'Harga satuan harus berupa angka',
            'harga_satuan.required' => 'Harga satuan harus diisi',

            'id_detail_panen.required' => 'Produk hasil panen harus dipilih',
            'id_detail_panen.exists' => 'Produk hasil panen tidak tersedia',


            'detail.*.subtotal.digits_between' => 'Subtotal minimal 1 digit dan maksimal 20 digit',
            'detail.*.subtotal.numeric' => 'Subtotal harus berupa angka',
            'detail.*.subtotal.required' => 'Subtotal harus diisi',

            'detail.*.diskon.between' => 'Diskon minimal 0 digit dan maksimal 8 digit',
            'detail.*.diskon.numeric' => 'Diskon harus berupa angka',
            'detail.*.diskon.required' => 'Diskon harus diisi',

            'detail.*.quantity.between' => 'Quantity minimal 1 digit dan maksimal 8 digit',
            'detail.*.quantity.numeric' => 'Quantity harus berupa angka',
            'detail.*.quantity.required' => 'Quantity harus diisi',

            'detail.*.harga_satuan.digits_between' => 'Harga satuan minimal 1 digit dan maksimal 20 digit',
            'detail.*.harga_satuan.numeric' => 'Harga satuan harus berupa angka',
            'detail.*.harga_satuan.required' => 'Harga satuan harus diisi',

            'detail.*.id_detail_panen.required' => 'Produk hasil panen harus dipilih',
            'detail.*.id_detail_panen.exists' => 'Produk hasil panen tidak tersedia',

            'change.digits_between' => 'Kembalian minimal 0 digit dan maksimal 20 digit',
            'change.numeric' => 'Kembalian harus berupa angka',
            'change.required' => 'Kembalian harus diisi',

            'pay.digits_between' => 'Pembayaran minimal 1 digit dan maksimal 20 digit',
            'pay.numeric' => 'Pembayaran harus berupa angka',
            'pay.required' => 'Pembayaran harus diisi',

            'total_netto.digits_between' => 'Total netto minimal 1 digit dan maksimal 20 digit',
            'total_netto.numeric' => 'Total netto harus berupa angka',
            'total_netto.required' => 'Total netto harus diisi',

            'potongan_harga.digits_between' => 'Potongan harga minimal 1 digit dan maksimal 20 digit',
            'potongan_harga.numeric' => 'Potongan harga harus berupa angka',
            'potongan_harga.required' => 'Potongan harga harus diisi',

            'total_bruto.digits_between' => 'Total bruto minimal 1 digit dan maksimal 20 digit',
            'total_bruto.numeric' => 'Total bruto harus berupa angka',
            'total_bruto.required' => 'Total bruto harus diisi',

            'customer.required' => 'Customer harus dipilih',
            'customer.exists' => 'Customer tidak tersedia',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $pay = $this->input('pay');
            $totalNetto = $this->input('total_netto');

            if ($pay < $totalNetto) {
                $validator->errors()->add('pay', 'Pembayaran tidak cukup');
            }
        });
    }
}
