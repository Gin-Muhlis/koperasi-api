<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MandatoryResource extends JsonResource
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
            'name' => $this->name,
            'position' => $this->position,
            'month_remain' => $this->handleRemainMonth($this->savings)
        ];
    }

    private function handleRemainMonth($data_savings) {
        $current_year = Carbon::now()->format('Y'); 
        $total_month = 12;

        $filtered_savings = [];

        foreach ($data_savings as $data) {
            if ($data->subCategory->name == 'simpanan wajib' && str_contains($data->month_year, $current_year)) {
                $filtered_savings[] = $data;
            }
        }

        $total_remain = $total_month - count($filtered_savings);

        return $total_remain;
    }
}
