<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PositionCategoryResource extends JsonResource
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
            'name' => $this->position,
            'simpanan pokok' => $this->pokok,
            'simpanan wajib' => $this->wajib,
            'simpanan wajib khusus' => $this->wajib_khusus
        ];
    }
}
