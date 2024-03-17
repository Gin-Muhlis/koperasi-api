<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubCategoryResource;

require_once app_path() . '/Helpers/helpers.php';

use App\Http\Requests\StoreSavingRequest;
use App\Http\Requests\UpdateSavingRequest;
use App\Models\Saving;
use App\Repositories\Member\MemberRepository;
use App\Repositories\PaymentDetermination\PaymentDeterminationRepository;
use App\Repositories\Saving\SavingRepository;
use App\Repositories\SubCategory\SubCategoryRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SavingController extends Controller
{
    private $savingRepo;
    private $memberRepo;
    private $subCategoryRepo;

    public function __construct(SavingRepository $savingRepository, MemberRepository $memberRepository, SubCategoryRepository $subCategoryRepository)
    {
        $this->savingRepo = $savingRepository;
        $this->memberRepo = $memberRepository;
        $this->subCategoryRepo = $subCategoryRepository;
    }
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        try {
            $sub_categories = $this->subCategoryRepo->getSubCategories();
            $members = $this->memberRepo->getMembers();

            $filtered_sub_categories = [];
            foreach ($sub_categories as $sub_category) {
                if ($sub_category->category->name == 'simpanan') {
                    $filtered_sub_categories[] = $sub_category;
                }
            }

            $members_data = $members->map(function ($member) use ($filtered_sub_categories) {
                $detail_savings = [];
                $history_savings = [];
                $total_saving = 0;

                foreach ($member->savings as $saving) {
                    $total_saving += $saving->amount;
                    $history_savings[] = [
                        'id' => $saving->id,
                        'amount' => $saving->amount,
                        'sub_category' => $saving->subCategory->name,
                        'date' => $saving->date->toDateString(),
                        'status' => $saving->status
                    ];
                }

                foreach ($filtered_sub_categories as $sub_category) {

                    // simpanan
                    $total = 0;
                    foreach ($member->savings as $saving) {
                        if ($saving->sub_category_id == $sub_category->id) {
                            $total += $saving->amount;
                        }
                    }

                    $detail_savings[$sub_category->name] = $total;
                }   

                $sorted_history = collect($history_savings)->sortByDesc('date')->values()->all();

                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'position' => $member->position,
                    'total_saving' => $total_saving,
                    'detail_savings' => $detail_savings,
                    'history_savings' => $sorted_history
                ];
            });

            return response()->json([
                'data' => $members_data
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function memberSaving()
    {
        try {

            $sub_categories = $this->subCategoryRepo->getSubCategories();
            $members = $this->memberRepo->getMembers();

            $filtered_sub_categories = [];
            foreach ($sub_categories as $sub_category) {
                if ($sub_category->category->name == 'simpanan' || $sub_category->category->name == 'piutang') {
                    $filtered_sub_categories[] = $sub_category;
                }
            }

            $members_data = $members->map(function ($member) use ($filtered_sub_categories) {
                $data_dinamis = [];

                foreach ($filtered_sub_categories as $sub_category) {
                    $detail = [];
                    $months_saving = [];

                    // simpanan
                    foreach ($member->savings as $saving) {
                        if ($sub_category->id == $saving->sub_category_id) {
                            $months_saving[] = [
                                'month_year' => $saving->month_year,
                                'status' => $saving->status,
                            ];
                        }

                        if ($saving->sub_category_id == $sub_category->id) {
                            $detail = [
                                'amount' => $saving->amount,
                                'sub_category_id' => $sub_category->id,
                                'type_payment' => $saving->SubCategory->type_payment,
                                'months_status' => $months_saving
                            ];
                        }
                    }

                    $data_dinamis[$sub_category->name] = $detail;
                }

                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'position' => $member->position,
                    'position_category_id' => $member->group_id,
                    'data' => $data_dinamis
                ];
            });

            return response()->json([
                'data' => $members_data
            ]);

        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function getSubCategories()
    {
        try {
            $sub_categories = $this->subCategoryRepo->getSubCategories();

            $filtered_sub_categories = [];
            foreach ($sub_categories as $sub_category) {
                if ($sub_category->category->name == 'simpanan') {
                    $filtered_sub_categories[] = $sub_category;
                }
            }

            usort($filtered_sub_categories, function ($a, $b) {
                return $a['id'] - $b['id'];
            });

            return response()->json([
                'data' => SubCategoryResource::collection($filtered_sub_categories)
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSavingRequest $request)
    {
        try {
            $validated = $request->validated();
            $sub_category = $this->subCategoryRepo->showSubCategory($validated['sub_category_id']);

            DB::beginTransaction();

            foreach ($validated['members'] as $member) {
                $time = $validated['month_year'];

                // cek apakah ada anggota yang sudah membayar simpanan pada bulan yang ditentukan
                if ($sub_category->type_payment == 'monthly') {
                    $is_already_saving = $this->savingRepo->getMemberSpesificSavings($member['id'], $sub_category->id);

                    if (count($is_already_saving) > 0 && $is_already_saving->contains('month_year', $time)) {
                        return response()->json([
                            'message' => "Terdapat anggota yang sudah membayar {$sub_category->name} pada bulan yang ditentukan",
                        ], 400);
                    }
                }

                $data = $this->generateSavingData($member['id'], $member['amount'], $sub_category->id, $validated['description'], $time);

                $this->savingRepo->makeSavingMembers($data);
            }

            DB::commit();

            return response()->json([
                'message' => 'Data simpanan anggota berhasil ditambahkan'
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return errorResponse($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */

    public function show(Saving $saving)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(UpdateSavingRequest $request, Saving $saving)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Saving $saving)
    {
        //
    }

    private function generateSavingData($member_id, $amount, $sub_category_id, $description, $month_year) {
		return [
			'uuid' => Str::uuid(),
			'code' => generateCode(),
			'member_id' => $member_id,
			'amount' => $amount,
			'date' => Carbon::now()->format('Y-m-d'),
			'sub_category_id' => $sub_category_id,
			'month_year' => $month_year,
			'user_id' => Auth::user()->id,
			'description' => $description,
            'status' => 'dibayar',
		];
	}
}
