<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use App\Http\Resources\RoleResource;
use App\Repositories\Role\RoleRepository;
use Exception;

class RoleController extends Controller {
	private $roleRepo;

	public function __construct(RoleRepository $roleRepository) {
		$this->roleRepo = $roleRepository;
	}

	public function index() {
		try {
			$data_roles = $this->roleRepo->getRoles();

			return response()->json([
				'data' => RoleResource::collection($data_roles),
			]);
		} catch (Exception $e) {
			return errorResponse($e->getMessage());
		}
	}
}
