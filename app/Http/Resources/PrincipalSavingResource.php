<?php

namespace App\Http\Resources;

use App\Models\PositionCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrincipalSavingResource extends JsonResource
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
            'member_name' => $this->name,
            'member_position' => $this->position,
        ];
    }
}
