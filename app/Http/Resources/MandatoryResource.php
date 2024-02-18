<?php

namespace App\Http\Resources;

use App\Repositories\SubCategory\SubCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MandatoryResource extends JsonResource {

	private $subCategoryRepo;
	public function __construct(SubCategoryRepository $subCategoryRepository) {
		$this->SubCategoryRepo = $subCategoryRepository;
	}
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
		$sub_category = $this->subCategoryRepo->getByName('simpanan wajib');
		$is_saving = count($data_savings);
		return $sub_category->id;
		if ($is_saving > 0 && $is_saving->contains('sub_category_id', $sub_category->id)) {
			$last_saving = $data_savings->last();

			return $last_saving->amount;
		}

		return null;
	}
}
