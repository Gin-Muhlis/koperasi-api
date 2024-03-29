<?php

namespace App\Repositories\Role;

use LaravelEasyRepository\Repository;

interface RoleRepository extends Repository {

	public function getRoles();
	public function searchRole($name);
}
