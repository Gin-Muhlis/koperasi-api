<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use App\Repositories\Member\MemberRepository;
use App\Repositories\SubCategory\SubCategoryRepository;
use Exception;

class ReportController extends Controller
{

    private $memberRepo;
    private $subCategoryRepo;

    public function __construct(SubCategoryRepository $subCategoryRepo, MemberRepository $memberRepository) {
        $this->subCategoryRepo = $subCategoryRepo;
        $this->memberRepo = $memberRepository;
    }

    public function reportMembers()
    {
        try {
            $sub_categories = $this->subCategoryRepo->getSubCategories();
            $members = $this->memberRepo->getMembers();

            $filtered_sub_categories = filterSavingLoanCategories($sub_categories);

            $members_data = $members->map(function ($member) use ($filtered_sub_categories) {
                $data_dinamis = [];

                foreach ($filtered_sub_categories as $sub_category) {
                    $detail = 0;

                    $total_saving = 0;
                    // simpanan
                    foreach ($member->savings as $saving) {
                        if ($saving->sub_category_id == $sub_category->id) {
                            $total_saving += $saving->amount;
                            $detail = $total_saving;
                        }
                    }

                    $total_loan = 0;
                    // pinjaman
                    foreach ($member->loans as $loan) {
                        if ($loan->sub_category_id == $sub_category->id) {
                            $total_loan += $loan->total_payment;
                            $detail = $total_loan;
                        }
                    }

                    $data_dinamis[$sub_category->name] = $detail;
                }

                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'list' => $data_dinamis
                ];
            });

            return response()->json([
                'data' => $members_data
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function reportSavingMembers()
    {
        try {
            $sub_categories = $this->subCategoryRepo->getSubCategories();
            $members = $this->memberRepo->getMembers();

            $filtered_sub_categories = filterSavingCategories($sub_categories);
            $members_data = $members->map(function ($member) use ($filtered_sub_categories) {
                $data_dinamis = [];

                foreach ($filtered_sub_categories as $sub_category) {
                    $detail = 0;

                    $total_saving = 0;
                    // simpanan
                    foreach ($member->savings as $saving) {
                        if ($saving->sub_category_id == $sub_category->id) {
                            $total_saving += $saving->amount;
                            $detail = $total_saving;
                        }
                    }

                    $data_dinamis[$sub_category->name] = $detail;
                }

                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'list' => $data_dinamis
                ];
            });

            return response()->json([
                'data' => $members_data
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function reportLoanMembers()
    {
        try {
            $sub_categories = $this->subCategoryRepo->getSubCategories();
            $members = $this->memberRepo->getMembers();

            $filtered_sub_categories = filterLoanCategories($sub_categories);

            $members_data = $members->map(function ($member) use ($filtered_sub_categories) {
                $data_dinamis = [];

                foreach ($filtered_sub_categories as $sub_category) {
                    $detail = 0;
                    $total_loan = 0;
                    // pinjaman
                    foreach ($member->loans as $loan) {
                        if ($loan->sub_category_id == $sub_category->id) {
                            $total_loan += $loan->total_payment;
                            $detail = $total_loan;
                        }
                    }

                    $data_dinamis[$sub_category->name] = $detail;
                }

                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'list' => $data_dinamis
                ];
            });

            return response()->json([
                'data' => $members_data
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }
}
