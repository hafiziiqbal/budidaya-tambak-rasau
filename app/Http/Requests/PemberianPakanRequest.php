<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PemberianPakanRequest extends FormRequest
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

                    'inputs.*.quantity' => 'required|numeric|between:0,99999999.99',
                    'inputs.*.id_pembagian_pakan' => 'required|exists:detail_pembagian_pakan,id',
                    'inputs.*.id_pembagian_bibit' => 'required|exists:detail_pembagian_bibit,id',
                ];
        } else {
            $validate =
                [

                    'id_pembagian_pakan' => 'required|exists:detail_pembagian_pakan,id',
                    'id_pembagian_bibit' => 'required|exists:detail_pembagian_bibit,id',
                    'quantity' => 'required|numeric|between:0,99999999.99',
                ];
        }

        return $validate;
    }

    public function messages()
    {
        return [
            'id_pembagian_pakan.required' => 'Pembagian pakan harus dipilih',
            'id_pembagian_pakan.exists' => 'Pembagian pakan tidak tersedia',

            'id_pembagian_bibit.required' => ' Pembagian bibit harus dipilih',
            'id_pembagian_bibit.exists' => ' Pembagian bibit tidak tersedia',

            'quantity.required' => 'Quantity harus diisi',
            'quantity.numeric' => 'Quantity harus berupa angka',
            'quantity.between' => 'Quantity minimal 0 digit dan maksimal 8 digit',


            'inputs.*.id_pembagian_pakan.required' => 'Pembagian pakan harus dipilih',
            'inputs.*.id_pembagian_pakan.exists' => 'Pembagian pakan tidak tersedia',

            'inputs.*.id_pembagian_bibit.required' => ' Pembagian bibit harus dipilih',
            'inputs.*.id_pembagian_bibit.exists' => ' Pembagian bibit tidak tersedia',

            'inputs.*.quantity.required' => 'Quantity harus diisi',
            'inputs.*.quantity.numeric' => 'Quantity harus berupa angka',
            'inputs.*.quantity.between' => 'Quantity minimal 0 digit dan maksimal 8 digit',
        ];
    }
}
