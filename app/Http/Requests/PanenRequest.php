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
            $validate =
                [
                    'tgl_panen' => 'required|date_format:d-m-Y',
                    'detail.*.quantity' => 'required|numeric|between:0,99999999.99',
                    'detail.*.status' => 'required',
                    'detail.*.id_detail_pembagian_bibit' => 'required|exists:detail_pembagian_bibit,id',
                ];
        }
        return $validate;
    }

    public function messages()
    {
        return [
            'tgl_panen.required' => 'Tanggal panen harus diisi',
            'tgl_panen.date_format' => 'Tanggal panen memiliki format d-m-Y',

            'detail.*.quantity.required' => 'Quantity harus diisi',
            'detail.*.quantity.numeric' => 'Quantity harus berupa angka',
            'detail.*.quantity.between' => 'Quantity minimal 0 digit dan maksimal 8 digit',

            'detail.*.status' => 'Status harus dipilih',

            'detail.*.id_detail_pembagian_bibit.required' => 'Ikan harus dipilih',
            'detail.*.id_detail_pembagian_bibit.exists' => 'Ikan tidak tersedia',
        ];
    }
}
