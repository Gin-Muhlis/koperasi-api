<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileAppResource extends JsonResource
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
            'chairmans_name'=> $this->chairmans_name, 
            'secretary_name'=> $this->secretary_name, 
            'treasurer_name'=> $this->treasurer_name, 
            'address' => $this->address,
        ];
    }
}
