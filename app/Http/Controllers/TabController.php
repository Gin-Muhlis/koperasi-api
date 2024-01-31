<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use App\Http\Resources\PrincipalSavingResource;
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

            foreach ($members as $member) {
                $member_savings = $member->savings;

                $isPrincipanSaving = $member_savings->contains(function ($object) {
                    return $object->subCategory->name = 'simpanan pokok';
                });

                if (!$isPrincipanSaving) {
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
}