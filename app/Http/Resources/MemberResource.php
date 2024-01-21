<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource {
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
			'email' => $this->email,
			'address' => $this->address,
			'phone_number' => $this->phone_number,
			'gender' => $this->gender,
			'position' => $this->position,
			'identity_number' => $this->identity_number,
			'religion' => $this->religion,
			'date_activation' => $this->date_activation,
			'imageProfile' => $this->image ? str_replace('public/', '', url("storage/{$this->image}")) : '-',
			'user' => [
				'uuid' => $this->user->uuid,
				'username' => $this->user->username,
				'active' => $this->user->active,
				'last_login_ip' => $this->user->last_login_ip,
				'last_login_time' => $this->user->last_login_time,
			],
		];
	}
}
