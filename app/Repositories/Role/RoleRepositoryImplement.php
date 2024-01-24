<?php

namespace App\Repositories\Role;

use LaravelEasyRepository\Implementations\Eloquent;
use Spatie\Permission\Models\Role;

class RoleRepositoryImplement extends Eloquent implements RoleRepository {

	protected $model;

	public function __construct(Role $model) {
		$this->model = $model;
	}

	public function getRoles() {
		return $this->all();
	}
}
