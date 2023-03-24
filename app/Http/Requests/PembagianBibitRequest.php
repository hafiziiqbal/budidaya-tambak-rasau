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
        $validateJaring = 'nullable';

        if ($this->type == 'store-all') {

            if ($this->jenis_pembagian == 'bibit') {
                $idDetailBeliValidate = 'required|exists:detail_beli,id';
                $idDetailPanenValidate = 'nullable';
            }
            if ($this->jenis_pembagian == 'sortir') {
                $idDetailPanenValidate = 'required|exists:detail_panen,id';
                $idDetailBeliValidate = 'nullable';
            }

            $details = $this->input('detail');
            $hasValidDetail = false;
            foreach ($details as $key => $detail) {
                if ($detail['id_jaring'] !== null && $this->hasDuplicateKolamId($detail['id_kolam'], $details, $key)) {
                    $hasValidDetail = true;
                    break;
                }
            }

            if (!$hasValidDetail) {
                foreach ($details as $key => $detail) {
                    if ($detail['id_jaring'] === null && $this->hasDuplicateKolamId($detail['id_kolam'], $details, $key)) {
                        $validateJaring = 'required';
                    }
                }
            }

            $validate =
                [
                    'tgl_pembagian' => 'required|date_format:d-m-Y',
                    'id_detail_beli' => $idDetailBeliValidate,
                    'id_detail_panen' => $idDetailPanenValidate,
                    'detail.*.quantity' => 'required|numeric|between:0,99999999.99',
                    'detail.*.id_jaring' => $validateJaring . '|distinct|unique:detail_pembagian_bibit,id_jaring',
                    'detail.*.id_kolam' => 'required|exists:master_kolam,id',
                ];
        }

        if ($this->type == 'update-detail') {
            $validate =
                [
                    'quantity' => 'required|numeric|between:0,99999999.99',
                    'id_jaring' => 'nullable|unique:detail_pembagian_bibit,id_jaring,' . $this->id,
                    'id_kolam' => 'required|exists:master_kolam,id',
                ];
        }
        if ($this->type == 'store-detail') {
            $validate =
                [
                    'quantity' => 'required|numeric|between:0,99999999.99',
                    'id_jaring' => 'nullable|unique:detail_pembagian_bibit,id_jaring',
                    'id_kolam' => 'required|exists:master_kolam,id',
                ];
        }
        return $validate;
    }

    public function messages()
    {
        return [
            'id_kolam.required' => 'Kolam harus dipilih',
            'id_kolam.exists' => 'Kolam tidak tersedia',

            'id_jaring.unique' => 'Jaring digunakan oleh data lain',

            'quantity.required' => 'Quantity harus diisi',
            'quantity.numeric' => 'Quantity harus berupa angka',
            'quantity.between' => 'Quantity minimal 0 digit dan maksimal 8 digit',

            'detail.*.id_jaring.required' => 'Jaring dengan kolam yang sama harus dipilih salah satu',
            'detail.*.id_jaring.distinct' => 'Jaring tidak boleh duplikat',
            'detail.*.id_jaring.unique' => 'Jaring digunakan oleh data lain',

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

    private function hasDuplicateKolamId($kolamId, $details, $currentIndex)
    {
        foreach ($details as $key => $detail) {
            if ($key !== $currentIndex && $detail['id_kolam'] === $kolamId) {
                return true;
            }
        }

        return false;
    }
}
