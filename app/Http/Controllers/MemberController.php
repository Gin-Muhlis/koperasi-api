<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\MemberResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreMemberRequest;
use App\Repositories\Loan\LoanRepository;
use App\Repositories\Role\RoleRepository;
use App\Repositories\User\UserRepository;
use App\Http\Requests\UpdateMemberRequest;
use App\Repositories\Member\MemberRepository;
use App\Repositories\Saving\SavingRepository;
use App\Repositories\Invoice\InvoiceRepository;
use App\Repositories\Installment\InstallmentRepository;
use App\Repositories\SubCategory\SubCategoryRepository;
use App\Repositories\PositionCategory\PositionCategoryRepository;

class MemberController extends Controller
{
    private $memberRepo;
    private $userRepo;
    private $savingRepo;
    private $installmentRepo;
    private $invoiceRepo;
    private $loanRepo;
    private $roleRepo;
    private $subCategoryRepo;
    private $positionCategoryRepo;

    public function __construct(MemberRepository $member, UserRepository $user, SavingRepository $savingRepository, InstallmentRepository $installmentRepository, InvoiceRepository $invoiceRepository, LoanRepository $loanRepository, RoleRepository $roleRepository, SubCategoryRepository $subCategoryRepository, PositionCategoryRepository $positionCategoryRepository)
    {
        $this->memberRepo = $member;
        $this->userRepo = $user;
        $this->savingRepo = $savingRepository;
        $this->installmentRepo = $installmentRepository;
        $this->invoiceRepo = $invoiceRepository;
        $this->loanRepo = $loanRepository;
        $this->roleRepo = $roleRepository;
        $this->subCategoryRepo = $subCategoryRepository;
        $this->positionCategoryRepo = $positionCategoryRepository;
    }

    public function index(Request $request)
    {
        try {
            $members = $this->memberRepo->getmembers();
            return response()->json([
                'data' => MemberResource::collection($members),
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function store(StoreMemberRequest $request)
    {
        try {
            $validated = $request->validated();

            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('public/member');
            }

            $data_member = generateDataMember('store', null, $validated);

            DB::beginTransaction();

            $member = $this->memberRepo->createMember($data_member);

            $data_user = generateDataUser('store', $member, $validated);

            $user = $this->userRepo->createUser($data_user);

            $role = $this->roleRepo->searchRole($validated['role']);

            if (is_null($role)) {
                return response()->json([
                    'message' => "Role {$validated['role']} tidak ditemukan"
                ], 400);
            }

            $user->assignRole($role->name);

            DB::commit();

            return response()->json([
                'role' => $validated['role'],
                'message' => 'Data angggota berhasil ditambahkan',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return errorResponse($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $member = $this->memberRepo->showMember($id);
            return response()->json([
                'data' => new MemberResource($member),
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function update(UpdateMemberRequest $request, $id)
    {
        try {
            $validated = $request->validated();

            $member = $this->memberRepo->showMember($id);

            if ($request->hasFile('image')) {
                if ($member->image) {
                    Storage::delete($member->image);
                }

                $validated['image'] = $request->file('image')->store('public/member');
            }

            $data_member = generateDataMember('update', $member, $validated);

            DB::beginTransaction();

            $this->memberRepo->updateMember($id, $data_member);

            $data_user = generateDataUser('update', $member, $validated);

            $this->userRepo->updateUser($member->user->id, $data_user);

            $role = $this->roleRepo->searchRole($validated['role']);

            if (is_null($role)) {
                return response()->json([
                    'message' => "Role {$validated['role']} tidak ditemukan"
                ], 400);
            }

            if ($member->user->getRoleNames()->first() != $validated['role']) {
                $member->user->syncRoles([$validated['role']]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Data anggota berhasil diperbarui',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return errorResponse($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->memberRepo->deleteMember($id);

            return response()->json([
                'message' => 'Data member berhasil dihapus',
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
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
    public function dashboardMember()
    {
        try {
            $user = Auth::user();

            $data_saving = [];

            $saving_members = $this->savingRepo->getSavingsMember($user->id);
            $sub_categories = $this->subCategoryRepo->getSubCategories();

            $filtered_sub_categories = filterSavingCategories($sub_categories);
            $sub_categories_loan = filterLoanCategories($sub_categories);

            $time = Carbon::now()->format('m-Y');
            // $not_payed = [];

            foreach ($filtered_sub_categories as $sub_category) {
                // if ($sub_category->type_payment == 'monthly') {
                //     $amount = 0;
                //     $is_payed = $this->savingRepo->getNotPayedSaving($user->id, $sub_category->id, $time);

                //     $savings_sub_category = $this->savingRepo->getMemberSpesificSavings($user->id, $sub_category->id);

                //     if (count($savings_sub_category) > 0) {

                //         $saving = $savings_sub_category->first();

                //         $amount = $saving->amount;
                //     } else {
                //         $position_category = $this->positionCategoryRepo->getPositionCategoryById($user->member->group_id);
                //         $sub_category_split = explode(' ', $sub_category);
                //         array_shift($sub_category_split);
                //         $sub_category_name = count($sub_category_split) > 1 ? implode('_', $sub_category_split) : $sub_category_split[0];
                //         $amount = $position_category->$sub_category_name;
                //     }

                //     if (!isset($is_payed)) {
                //         $not_payed[] = [
                //             'id' => $sub_category->id,
                //             'category' => $sub_category->name,
                //             'amount' => $amount
                //         ];
                //     }
                // }

                // simpanan
                $total = 0;
                foreach ($saving_members as $saving) {

                    if ($saving->sub_category_id == $sub_category->id) {
                        $total += $saving->amount;
                    }
                }

                $data_saving[$sub_category->name] = $total;
            }

            // $year = Carbon::now()->year;
            // $month = Carbon::now()->month;

            // foreach ($sub_categories_loan as $sub_category) {
            //     if ($sub_category->type_payment == 'monthly') {
            //         $amount = 0;
            //         $is_loan = $this->loanRepo->getLoanMemberBySubCategory($sub_category->id, $user->id);

            //         if (isset($is_loan)) {
            //             $is_payed = $this->installmentRepo->getNotPayedInstallment($is_loan->id, $year, $month);
            //             if (!isset($is_payed)) {
            //                 $amount = ceil($is_loan->total_payment / $is_loan->loan_duration / 1000) * 1000;
            //                 $not_payed[] = [
            //                     'id' => $sub_category->id,
            //                     'category' => $sub_category->name,
            //                     'amount' => $amount
            //                 ];
            //             }
            //         }
            //     }
            // }

            $history_savings = $this->savingRepo->getHistorySavingmember($user->id);
            $history_installments = $this->installmentRepo->getHistoryInstallments($user->id);

            $result_saving = [];
            $result_installment = [];

            foreach ($history_savings as $saving) {
                $time_split = explode(' ', Carbon::parse($saving->created_at)->format('Y-m-d H:i:s'));
                $result_saving[] = [
                    'amount' => $saving->amount,
                    'date' => $time_split[0],
                    'time' => $time_split[1],
                    'sub_category' => $saving->subCategory->name,
                ];
            }

            foreach ($history_installments as $installment) {
                $time_split = explode(' ', Carbon::parse($installment->created_at)->format('Y-m-d H:i:s'));
                $result_installment[] = [
                    'amount' => $installment->amount,
                    'date' => $time_split[0],
                    'time' => $time_split[1],
                    'sub_category' => $installment->subCategory->name,
                ];
            }

            $data = [
                'data_saving' => $data_saving,
                'history_savings' => $result_saving,
                'history_installments' => $result_installment,
                // 'not_payed' => $not_payed
            ];

            return response()->json([
                'data' => $data
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function dashboardAdmin()
    {
        try {
            $count_members = $this->memberRepo->getCountMembers();
            $count_invoices_not_paid = $this->invoiceRepo->getNotPaidInvoices();
            $count_invoices_paid = $this->invoiceRepo->getPaidInvoices();
            $total_savings = $this->savingRepo->getTotalSavings();
            $total_loans = $this->loanRepo->getTotalLoans();
            $historyInvoices = $this->invoiceRepo->getHistoryInvoices();
            $loan_members = $this->loanRepo->getLoanMembers();
            $position_categories = $this->positionCategoryRepo->getPositionCategories();

            $loan_grafik = $this->generateDataLoan();


            $list_position_categories = [];

            foreach ($position_categories as $position) {
                $total = 0;
                foreach ($loan_members as $loan) {
                    if ($loan->member->group_id == $position->id) {
                        $total += $loan->total_payment;
                    }
                }
                $list_position_categories[] = [
                    'id' => $position->id,
                    'position' => $position->position,
                    'total' => $total
                ];
            }

            $array_object_position_categories = collect($list_position_categories);

            $top_position_categories_loan = $array_object_position_categories->sortByDesc('total');

            $result_position = [];

            foreach ($top_position_categories_loan as $position) {
                $result_position[] = [
                    'id' => $position['id'],
                    'position' => $position['position'],
                    'total' => $position['total']
                ];
            }


            $data = [
                'count_member' => $count_members,
                'count_invoices_not_paid' => $count_invoices_not_paid,
                'count_invoices_paid' => $count_invoices_paid,
                'total_savings' => $total_savings,
                'total_loans' => $total_loans,
                'history_invoices' => $historyInvoices,
                'top_position_categories_loan' => $result_position,
                'loan_grafik' => $loan_grafik
            ];

            return response()->json([
                'data' => $data
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    private function generateDataLoan()
    {
        $months = [
            '01',
            '02',
            '03',
            '04',
            '05',
            '06',
            '07',
            '08',
            '09',
            '10',
            '11',
            '12',
        ];

        $year = Carbon::now()->year;

        $result_paid = [];
        $result_not_paid = [];

        foreach ($months as $month) {
            $total_paid = $this->loanRepo->getSumPaidLoanByMonth($month, $year);
            $total_not_paid = $this->loanRepo->getSumNotPaidLoanByMonth($month, $year);

            $result_paid[] = $total_paid;
            $result_not_paid[] = $total_not_paid;
        }

        return [
            'paid' => $result_paid,
            'not_paid' => $result_not_paid
        ];
    }
}
