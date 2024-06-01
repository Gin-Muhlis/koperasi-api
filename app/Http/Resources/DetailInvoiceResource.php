<?php

namespace App\Http\Resources;

use App\Models\Installment;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Saving;
use App\Models\SubCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailInvoiceResource extends JsonResource {
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
			'payment_data' => [
				'time' => $this->payment?->created_at ? Carbon::parse($this->payment->created_at)->format('Y-m-d H:i:s') : null,
				'payer' => $this->payment?->payer ?? null,
				'payment_method' => $this->payment?->payment_method ?? null,
				'norek' => $this->payment?->norek ?? null
			],
			'details' => $this->handleData($this->savings, $this->installments),
		];
	}

	private function handleData($savings, $installments) {
		
		$sub_categories = SubCategory::all();
		$filtered_sub_categories = [];
			foreach ($sub_categories as $sub_category) {
				if ($sub_category->category->name == 'simpanan' || $sub_category->category->name == 'piutang') {
					$filtered_sub_categories[] = $sub_category;
				}
			}

		$result = [];
		$members_id = [];

		foreach ($savings as $saving) {
			if (!in_array($saving->member_id, $members_id)) {
				$members_id[] = $saving->member_id;
			}
		}

		foreach ($installments as $installment) {
			if (!in_array($installment->member_id, $members_id)) {
				$members_id[] = $installment->loan->member_id;
			}
		}

		foreach ($members_id as $id) {
			$member = Member::findOrFail($id);
			$data_dinamis = [];
			foreach ($filtered_sub_categories as $sub_category) {
				if ($sub_category->category->name == 'simpanan') {
					// saving
				$saving_member = collect($savings)->where('sub_category_id', $sub_category->id)->where('member_id', $id)->first();

				// $sub_category_name = str_replace(' ', '_', $sub_category->name);

				$data_dinamis[$sub_category->name] = $saving_member?->amount ?? 0;
				} else {
					// installment
				$installment_member = collect($installments)->where('sub_category_id', $sub_category->id)->where('member_id', $id)->first();

				$data_dinamis[$sub_category->name] = $installment_member	?->amount ?? 0;
				}
				
			}

			$result[] = [
				'member_id' => $id,
				'member_name' => $member->name,
				...$data_dinamis
			];
		}

		usort($result, function ($a, $b) {
			return $a['member_id'] - $b['member_id'];
		});

		return $result;
	}
}
