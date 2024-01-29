<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource {
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array {
		return [
			'id' => $this->id,
			'uuid' => $this->uuid,
			'code' => $this->code,
			'type' => $this->type,
			'name' => $this->name,
			'category' => $this->category->name,
			'category_id' => $this->category->id
		];
	}
}
