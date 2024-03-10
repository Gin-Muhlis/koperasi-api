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

			$members_data = $members->map(function($member) use ($filtered_sub_categories) {
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
                
				return [
					'id' => $member->id,
					'name' => $member->name,
					'position' => $member->position,
                    'total_saving' => $total_saving,
					'detail_savings' => $detail_savings,
                    'history_savings' => $history_savings
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
            $user = Auth::user();
            $validated = $request->validated();

            DB::beginTransaction();

            foreach ($validated['members'] as $member) {
                if ($validated['type_saving'] != 'simpanan sukarela' && $validated['type_saving'] != 'tabungan rekreasi') {
                    $is_mandatory_saving = $this->savingRepo->getMemberSpesificSavings($member['id'], $validated['sub_category_id']);

                    if (count($is_mandatory_saving) > 0 && $is_mandatory_saving->contains('month_year', $validated['month_year'])) {
                        return response()->json([
                            'message' => 'Terdapat data member yang sudah membayar simpanan pada bulan yang ditentukan',
                        ], 400);
                    }
                }

                $data = [
                    'uuid' => Str::uuid(),
                    'code' => generateCode(),
                    'member_id' => $member['id'],
                    'amount' => $member['payment'],
                    'date' => Carbon::now()->format('Y-m-d'),
                    'sub_category_id' => $validated['sub_category_id'],
                    'month_year' => $validated['month_year'],
                    'user_id' => $user->id,
                    'description' => $validated['description'] ?? '-',
                    'status' => 'dibayar'
                ];

                $this->savingRepo->makeSavingMembers($data);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Simpanan member berhasil ditambahkan',
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
}
