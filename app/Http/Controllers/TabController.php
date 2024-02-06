<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use App\Http\Resources\MandatoryResource;
use App\Http\Resources\PrincipalSavingResource;
use App\Http\Resources\ReceivableResource;
use App\Repositories\Member\MemberRepository;
use Exception;
use Illuminate\Http\Request;

class TabController extends Controller
{
    private $memberRepo;

    public function __construct(MemberRepository $memberRepository)
    {
        $this->memberRepo = $memberRepository;
    }
    public function principalSaving()
    {
        try {
            $members = $this->memberRepo->getMembers();

            $member_principaL_saving = [];
            $filtered_members = $this->filterMember(($members));
            foreach ($filtered_members as $member) {
                $member_savings = $member->savings;

                $isPrincipalSaving = $member_savings->contains(function ($object) {
                    return $object->subCategory->name = 'simpanan pokok';
                });

                if (!$isPrincipalSaving) {
                    $member_principaL_saving[] = $member;
                }
            }

            return response()->json([
                'data' => PrincipalSavingResource::collection($member_principaL_saving)
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function mandatorySaving()
    {
        try {
            $members = $this->memberRepo->getMembers();

            $filtered_members = $this->filterMember(($members));

            return response()->json([
                'data' => MandatoryResource::collection($filtered_members)
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function receivable()
    {
        try {
            $members = $this->memberRepo->getMembers();

            $filtered_members = $this->filterMember(($members));

            return response()->json([
                'data' => ReceivableResource::collection($filtered_members)
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    private function filterMember($data)
    {
        $filtered_members = [];

        foreach ($data as $member) {
            if (!$member->user->hasRole('super-admin')) {
                $filtered_members[] = $member;
            }
        }

        return $filtered_members;
    }
}
