<?php

namespace App\Repositories\Saving;

use LaravelEasyRepository\Repository;

interface SavingRepository extends Repository {

	public function getMemberSpesificSavings($member_id, $sub_category_id);
	public function makeSavingMembers($data);
}
