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
            'name'=> $this->name, 
            'icon' => $this->icon ? str_replace('public/', '', url("storage/{$this->icon}")) : config('app.url') . '/images/profile-default.png',
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'about' => $this->about,
        ];
    }
}
