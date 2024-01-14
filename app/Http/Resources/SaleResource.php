<?php

namespace App\Http\Resources;

require_once app_path() . '/Helpers/helpers.php';

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
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
            'date' => generateDate($this->date->toDateString()),
            'code' => $this->code,
            'total' => $this->total_payment,
            'sub_category' => [
                'id' => $this->subCategory->id,
                'name' => $this->subCategory->name,
            ],
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->username,
            ],
        ];
    }
}
