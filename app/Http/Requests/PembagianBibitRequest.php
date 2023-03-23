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
        $validate = '';
        $idDetailBeliValidate = '';
        $idDetailPanenValidate = '';

        if ($this->type == 'store-all') {

            if ($this->jenis_pembagian == 'bibit') {
                $idDetailBeliValidate = 'required|exists:detail_beli,id';
                $idDetailPanenValidate = 'nullable';
            }
            if ($this->jenis_pembagian == 'sortir') {
                $idDetailPanenValidate = 'required|exists:detail_panen,id';
                $idDetailBeliValidate = 'nullable';
            }

            $validate =
                [
                    'tgl_pembagian' => 'required|date_format:d-m-Y',
                    'id_detail_beli' => $idDetailBeliValidate,
                    'id_detail_panen' => $idDetailPanenValidate,
                    'detail.*.quantity' => 'required|numeric|between:0,99999999.99',
                    'detail.*.id_jaring' => 'nullable|distinct',
                    'detail.*.id_kolam' => 'required|exists:master_kolam,id',
                ];
        }
        return $validate;
    }

    public function messages()
    {
        return [
            'detail.*.id_jaring.distinct' => 'Jaring tidak boleh duplikat',

            'detail.*.id_kolam.required' => 'Kolam harus dipilih',
            'detail.*.id_kolam.exists' => 'Kolam tidak tersedia',

            'detail.*.quantity.required' => 'Quantity harus diisi',
            'detail.*.quantity.numeric' => 'Quantity harus berupa angka',
            'detail.*.quantity.between' => 'Quantity minimal 0 digit dan maksimal 8 digit',

            'id_detail_panen.required' => 'Sortir harus dipilih',
            'id_detail_panen.exists' => 'Sortir tidak tersedia',

            'id_detail_beli.required' => 'Bibit harus dipilih',
            'id_detail_beli.exists' => 'Bibit tidak tersedia',

            'tgl_pembagian.required' => 'Tanggal pembagian harus diisi',
            'tgl_pembagian.date_format' => 'Tanggal pembagian memiliki format d-m-Y',
        ];
    }
}
