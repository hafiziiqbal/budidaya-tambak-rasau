<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PembagianPakanRequest extends FormRequest
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
                    'tgl_pembagian' => 'required|date_format:d-m-Y',
                    'detail.*.quantity' => 'required|numeric|between:0,99999999.99',
                    'detail.*.id_tong' => 'required|distinct|unique:detail_pembagian_pakan,id_tong',
                    'detail.*.id_detail_beli' => 'required|exists:detail_beli,id',
                ];
        }

        if ($this->type == 'store-detail') {
            $validate =
                [
                    'quantity' => 'required|numeric|between:0,99999999.99',
                    'id_tong' => 'required|distinct|unique:detail_pembagian_pakan,id_tong',
                    'id_detail_beli' => 'required|exists:detail_beli,id',
                ];
        }

        if ($this->type == 'update-detail') {
            $validate =
                [
                    'quantity' => 'required|numeric|between:0,99999999.99',
                    'id_tong' => 'required|unique:detail_pembagian_pakan,id_tong,' . $this->id,
                    'id_detail_beli' => 'required|exists:detail_beli,id',
                ];
        }
        return $validate;
    }

    public function messages()
    {
        return [
            'id_detail_beli.required' => 'Pakan harus dipilih',
            'id_detail_beli.exists' => 'Pakan tidak tersedia',

            'id_tong.required' => 'Tong harus dipilih',
            'id_tong.unique' => 'Tong digunakan oleh data lain',

            'quantity.required' => 'Quantity harus diisi',
            'quantity.numeric' => 'Quantity harus berupa angka',
            'quantity.between' => 'Quantity minimal 0 digit dan maksimal 8 digit',

            'detail.*.id_detail_beli.required' => 'Pakan harus dipilih',
            'detail.*.id_detail_beli.exists' => 'Pakan tidak tersedia',

            'detail.*.id_tong.required' => 'Tong harus dipilih',
            'detail.*.id_tong.distinct' => 'Tong tidak boleh duplikat',
            'detail.*.id_tong.unique' => 'Tong digunakan oleh data lain',

            'detail.*.quantity.required' => 'Quantity harus diisi',
            'detail.*.quantity.numeric' => 'Quantity harus berupa angka',
            'detail.*.quantity.between' => 'Quantity minimal 0 digit dan maksimal 8 digit',

            'tgl_pembagian.required' => 'Tanggal pembagian harus diisi',
            'tgl_pembagian.date_format' => 'Tanggal pembagian memiliki format d-m-Y',
        ];
    }
}
