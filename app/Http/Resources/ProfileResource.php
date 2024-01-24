<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource {
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array {
		return [
			'id' => $this->member->id,
			'uuid' => $this->member->uuid,
			'name' => $this->member->name,
			'email' => $this->member->email,
			'address' => $this->member->address,
			'phone_number' => $this->member->phone_number,
			'gender' => $this->member->gender,
			'position' => $this->member->position,
			'identity_number' => $this->member->identity_number,
			'religion' => $this->member->religion,
			'date_activation' => $this->member->date_activation,
			'imageProfile' => $this->member->image ? str_replace('public/', '', url("storage/{$this->member->image}")) : config('app.url') . '/images/profile-default.png',
			'username' => $this->username,
			'active' => $this->active,
			'role' => $this->getRoleNames()->first(),
			'last_login_ip' => $this->last_login_ip,
			'last_login_time' => $this->last_login_time,
		];
	}
}
