<?php

namespace App\Repositories\Member;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Member;

class MemberRepositoryImplement extends Eloquent implements MemberRepository{

 
    protected $model;

    public function __construct(Member $model)
    {
        $this->model = $model;
    }

    public function getMembers() 
    {
        return $this->all();
    }

    public function createMember($request)
    {
        return $this->create($request);
    }

    public function showMember($id) 
    {
        return $this->findOrFail($id);
    }

    public function updateMember($id, $request) 
    {
        return $this->update($id, $request);
    }

    public function deleteMember($id)
    {
        return $this->delete($id);
    }

    public function getSavingMembers()
    {
        return $this->model->with('savings.subCategory')->get();
    }
}
