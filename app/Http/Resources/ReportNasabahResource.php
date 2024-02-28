<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportNasabahResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'principal_saving' => $this->handleSaving($this->savings, 'simpanan pokok'),
            'mandatory_saving' => $this->handleSaving($this->savings, 'simpanan wajib'),
            'special_mandatory_saving' => $this->handleSaving($this->savings, 'simpanan wajib khusus'),
            'voluntary_saving' => $this->handleSaving($this->savings, 'simpanan sukarela'),
            'recretional_saving' => $this->handleSaving($this->savings, 'tabungan rekreasi'),
            'receivable' => $this->handleReceivable($this->loans, 'piutang s/p'),
        ];
    }

    private function handleSaving($data_savings, $sub_category) {
        if (count($data_savings) < 1) {
            return 0;
        }
        $total = 0;
        foreach ($data_savings as $data) {
            if ($data->subCategory->name == $sub_category) {
                $total += $data->amount;
            }
        }

        return $total;
    }

    private function handleReceivable($data_receivable, $sub_category) {
        if (count($data_receivable) < 1) {
            return 0;
        }
        $total = 0;
        foreach ($data_receivable as $data) {
            if ($data->subCategory->name == $sub_category) {
                $total += $data->total_payment;
            }
        }

        return $total;
    }
}
