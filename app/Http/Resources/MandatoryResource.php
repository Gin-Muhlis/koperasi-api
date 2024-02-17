<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MandatoryResource extends JsonResource {
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
			'payment' => $this->handlePayment($this->savings) ?? $this->positionCategory->wajib,
		];
	}

	private function handlePayment($data_savings) {
		$is_saving = count($data_savings);

		if ($is_saving > 0) {
			$last_saving = $data_savings->last();

			return $last_saving->amount;
		}

		return null;
	}
}
