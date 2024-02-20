<?php

namespace App\Http\Resources;

use App\Models\Installment;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Saving;
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
			'payment_method' => $this->payment_method,
			'payment_date' => $this->payment_date,
			'details' => $this->handleSavings($this->savings, $this->installments, $this->id),
		];
	}

	private function handleSavings($savings, $installments, $invoice_id) {
		$result = [];
		$members_id = [];

		foreach ($savings as $saving) {
			if (!in_array($saving->member_id, $members_id)) {
				$members_id[] = $saving->member_id;
			}
		}

		foreach ($installments as $installment) {
			if (!in_array($installment->loan->member_id, $members_id)) {
				$members_id[] = $installment->loan->member_id;
			}
		}

		foreach ($members_id as $id) {
			$member = Member::findOrFail($id);
			$savings_member = Saving::with('subCategory')->where([
				['member_id', $id],
				['invoice_id', $invoice_id],
			])->get();

			$principal_saving = $savings_member->filter(function ($saving) {
				return $saving->subCategory->name === 'simpanan pokok';
			})->first();

			$mandatory_saving = $savings_member->filter(function ($saving) {
				return $saving->subCategory->name === 'simpanan wajib';
			})->first();

			$special_mandatory_saving = $savings_member->filter(function ($saving) {
				return $saving->subCategory->name === 'simpanan wajib khusus';
			})->first();

			$voluntary_saving = $savings_member->filter(function ($saving) {
				return $saving->subCategory->name === 'simpanan sukarela';
			})->first();

			$recretional_saving = $savings_member->filter(function ($saving) {
				return $saving->subCategory->name === 'tabungan rekreasi';
			})->first();

			$loan_member = Loan::where([
				['member_id', $id],
				['status', '!=', 'lunas'],
			])->latest()->first();

			$receivable = null;
			$account_receivable = null;

			if ($loan_member) {

				$installments_member = Installment::with('subCategory')->where([
					['loan_id', $loan_member->id],
					['invoice_id', $invoice_id],
				])->get();

				$receivable = $installments_member->filter(function ($installment) {
					return $installment->subCategory->name = 'piutang s/p';
				})->first();

				$account_receivable = $installments_member->filter(function ($installment) {
					return $installment->subCategory->name = 'piutang dagang';
				})->first();
			}

			$result[] = [
				'memberId' => $id,
				'memberName' => $member->name,
				'principalSaving' => $principal_saving?->amount ?? 0,
				'mandatorySaving' => $mandatory_saving?->amount ?? 0,
				'specialMandatorySaving' => $special_mandatory_saving?->amount ?? 0,
				'voluntarySaving' => $voluntary_saving?->amount ?? 0,
				'recretionalSaving' => $recretional_saving?->amount ?? 0,
				'receivable' => $receivable?->amount ?? 0,
				'accountReceivable' => $account_receivable?->amount ?? 0,
			];
		}

		usort($result, function ($a, $b) {
			return $a['memberId'] - $b['memberId'];
		});

		return $result;
	}
}
