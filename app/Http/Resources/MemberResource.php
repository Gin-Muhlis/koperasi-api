<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
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
			'uuid' => $this->uuid,
			'name' => $this->name,
			'email' => $this->email,
			'address' => $this->address,
			'phone_number' => $this->phone_number,
			'gender' => $this->gender,
			'position' => $this->position,
			'identity_number' => $this->identity_number,
			'payment_member' => count($this->paymentDeterminations) > 0 ? $this->getPaymentMember($this->paymentDeterminations) : 0,
			'religion' => $this->religion,
			'date_activation' => $this->date_activation,
			'imageProfile' => $this->image ? str_replace('public/', '', url("storage/{$this->image}")) : config('app.url') . '/images/profile-default.png',
			'username' => $this->user->username,
			'active' => $this->user->active,
			'role' => $this->user->getRoleNames()->first(),
			'last_login_ip' => $this->user->last_login_ip,
			'last_login_time' => $this->user->last_login_time,
		];
	}

	private function getPaymentMember($payments)
	{
		$now = Carbon::now()->format('m-Y');
		foreach ($payments as $payment) {
			if ($payment->payment_month == $now) {
				return $payment->amount;
			}
		};
	}
}
