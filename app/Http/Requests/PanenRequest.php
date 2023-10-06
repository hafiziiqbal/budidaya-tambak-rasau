<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PanenRequest extends FormRequest
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
            $rules = [];
            $rules['tgl_panen'] = 'required|date_format:d-m-Y';
            foreach ($this->input('detail') as $key => $detail) {
                $rules["detail.$key.status"] = 'required';
                $rules["detail.$key.id_detail_pembagian_bibit"] = 'required|exists:detail_pembagian_bibit,id';
                $rules["detail.$key.id_produk"] = 'required|exists:produk,id';
                if ($detail['status'] == 1) {
                    $rules["detail.$key.quantity_berat"] = 'required|numeric|between:0,99999999.99';
                    $rules["detail.$key.quantity"] = 'required|numeric|between:0,99999999.99';
                    $rules["detail.$key.id_produk"] = 'required';
                } else {
                    $rules["detail.$key.quantity_berat"] = 'nullable|numeric|between:0,99999999.99';
                    $rules["detail.$key.quantity"] = 'required|numeric|between:0,99999999.99';
                    $rules["detail.$key.id_produk"] = 'nullable';
                }
            }
            $validate = $rules;
        }
        if ($this->type == 'update-header') {
            $validate =
                [
                    'tgl_panen' => 'required|date_format:d-m-Y',
                ];
        }
        if ($this->type == 'update-detail') {
            if ($this->status == 1) {
                $validate =
                    [
                        'quantity' => 'required|numeric|between:0,99999999.99',
                        'status' => 'required',
                        'id_detail_pembagian_bibit' => 'required|exists:detail_pembagian_bibit,id',
                        'id_produk' => 'required'
                    ];
            } else {
                $validate =
                    [
                        'quantity' => 'required|numeric|between:0,99999999.99',
                        'status' => 'required',
                        'id_detail_pembagian_bibit' => 'required|exists:detail_pembagian_bibit,id',
                    ];
            }
        }
        if ($this->type == 'store-detail') {
            if ($this->status  == 1) {
                $validate =  [
                    'quantity_berat' => 'required|numeric|between:0,99999999.99',
                    'quantity' => 'required|numeric|between:0,99999999.99',
                    'status' => 'required',
                    'id_detail_pembagian_bibit' => 'required|exists:detail_pembagian_bibit,id',
                ];
            } else {
                $validate =  [
                    'quantity' => 'required|numeric|between:0,99999999.99',
                    'status' => 'required',
                    'id_detail_pembagian_bibit' => 'required|exists:detail_pembagian_bibit,id',
                ];
            }
        }
        return $validate;
    }

    public function messages()
    {
        return [
            'id_produk' => 'Produk Ikan Siap Jual harus diisi',
            'quantity.required' => 'Quantity harus diisi',
            'quantity.numeric' => 'Quantity harus berupa angka',
            'quantity.between' => 'Quantity minimal 0 digit dan maksimal 8 digit',

            'status' => 'Status harus dipilih',

            'id_detail_pembagian_bibit.required' => 'Ikan harus dipilih',
            'id_detail_pembagian_bibit.exists' => 'Ikan tidak tersedia',

            'tgl_panen.required' => 'Tanggal panen harus diisi',
            'tgl_panen.date_format' => 'Tanggal panen memiliki format d-m-Y',

            'detail.*.id_produk.required' => 'Produk Ikan Siap Jual harus dipilih',
            'detail.*.id_produk.exists' => 'Produk Ikan Siap Jual tidak ada di data',

            'detail.*.quantity.required' => 'Quantity harus diisi',
            'detail.*.quantity.numeric' => 'Quantity harus berupa angka',
            'detail.*.quantity.between' => 'Quantity minimal 0 digit dan maksimal 8 digit',

            'detail.*.quantity_berat.required' => 'Total KG harus diisi',
            'detail.*.quantity_berat.numeric' => 'Total KG harus berupa angka',
            'detail.*.quantity_berat.between' => 'Total KG minimal 0 digit dan maksimal 8 digit',

            'detail.*.status' => 'Status harus dipilih',

            'detail.*.id_detail_pembagian_bibit.required' => 'Ikan harus dipilih',
            'detail.*.id_detail_pembagian_bibit.exists' => 'Ikan tidak tersedia',
        ];
    }
}
