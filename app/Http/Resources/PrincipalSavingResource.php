<?php

namespace App\Http\Resources;

use App\Models\PositionCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrincipalSavingResource extends JsonResource {
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array {
		return [
			'id' => $this->id,
			'name' => $this->name,
			'position' => $this->position,
			'payment' => $this->positionCategory->pokok,
		];
	}
}
