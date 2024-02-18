<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceivableResource extends JsonResource {
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array {
		return [
			'id' => $this->id,
			'name' => $this->name,
			'loan_id' => $this->loans->first()->id,
			'total_payment' => $this->loans->first()->total_payment,
			'paid' => $this->handlePaid($this->loans->first()->installments),
			'remain_payment' => $this->loans->first()->total_payment - $this->handlePaid($this->loans->first()->installments),
			'monthly' => ceil($this->loans->first()->total_payment / $this->loans->first()->loan_duration / 1000) * 1000,
		];
	}

	private function handlePaid($data) {
		if (count($data) < 1) {
			return 0;
		}

		$totalPaid = 0;
		foreach ($data as $item) {
			$totalPaid += $item->amount;
		}
		return $totalPaid;
	}
}
