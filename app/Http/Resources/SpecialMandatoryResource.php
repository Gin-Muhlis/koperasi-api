<?php

namespace App\Http\Resources;

use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecialMandatoryResource extends JsonResource
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
			'name' => $this->name,
			'position' => $this->position,
			'payment' => $this->handlePayment($this->savings) ?? $this->positionCategory->wajib_khusus,
		];
	}

	private function handlePayment($data_savings)
	{
		$sub_category = SubCategory::where('name', 'simpanan wajib khusus')->first();
		$is_saving = count($data_savings);

		if ($is_saving > 0 && $data_savings->contains('sub_category_id', $sub_category->id)) {
			$last_saving = $data_savings->last();

			return $last_saving->amount;
		}

		return null;
	}
}
