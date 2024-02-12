<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource {
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array {
		return [
			'id' => $this->id,
			'uuid' => $this->id,
			'name' => $this->member->name,
			'principal_saving' => $this->principal_saving,
			'mandatory_saving' => $this->mandatory_saving,
			'special_mandatory_saving' => $this->special_mandatory_saving,
			'voluntary_saving' => $this->voluntary_saving,
			'recretional_saving' => $this->recretional_saving,
			'receivable' => $this->receivable,
			'account_receivable' => $this->account_receivable,
			'month_year' => $this->month_year,
		];
	}
}
