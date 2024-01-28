<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StuffResource extends JsonResource {
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array {
		return [
			'id' => $this->id,
			'uuid' => $this->uuid,
			'name' => $this->name,
			'price' => $this->price,
			'image' => str_replace('public/', '', url("storage/{$this->image}")),
			'product_id' => $this->product->id,
			'product_name' => $this->product->name,

		];
	}
}
