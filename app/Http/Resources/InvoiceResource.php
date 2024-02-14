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
			'invoice_code' => $this->invoice_code,
			'invoice_name' => $this->invoice_name,
			'payment_source' => $this->payment_source,
			'status' => $this->status,
			'date' => $this->date,
			'due_date' => $this->due_date,
			'payment_method' => $this->payment_method,
			'payment_date' => $this->payment_date,
			'user' => $this->user->username,
		];
	}
}
