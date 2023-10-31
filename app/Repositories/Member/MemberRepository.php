<?php

namespace App\Repositories\Member;

use LaravelEasyRepository\Repository;

interface MemberRepository extends Repository{

    public function getMembers();
    public function createMember($request);
    public function showMember($id);
    public function updateMember($id, $request);
    public function deleteMember($id);
    public function getSavingMembers();
}
