<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SavingMemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'nama' => $this->name,
            'simpanan_wajib' => !$this->savings->isEmpty() ? $this->generateSavingData($this->savings, 'simpanan wajib') : 0,
            'simpanan_sukarela' => !$this->savings->isEmpty() ? $this->generateSavingData($this->savings, 'simpanan sukarela') : 0,
            'piutang sp' => !$this->savings->isEmpty() ? $this->generateSavingData($this->savings, 'piutang s/p') : 0,
            'piutang_jasa' => !$this->savings->isEmpty() ? $this->generateSavingData($this->savings, 'piutang jasa') : 0,
            'piutang_barang' => !$this->savings->isEmpty() ? $this->generateSavingData($this->savings, 'piutang barang') : 0,
            'tabungan_rekreasi' => !$this->savings->isEmpty() ? $this->generateSavingData($this->savings, 'tabungan rekreasi') : 0,
        ];
    }

    private function generateSavingData($dataSavings, $type)
    {
        $dataQurban = [];

        foreach ($dataSavings as $data) {
            if ($data->subCategory->name == $type) {
                $dataQurban[] = $data->amount;
            }
        }

        return array_sum($dataQurban);
    }
}
