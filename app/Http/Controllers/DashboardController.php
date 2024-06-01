<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use App\Repositories\Installment\InstallmentRepository;
use App\Repositories\Invoice\InvoiceRepository;
use App\Repositories\Loan\LoanRepository;
use App\Repositories\Member\MemberRepository;
use App\Repositories\PositionCategory\PositionCategoryRepository;
use App\Repositories\Role\RoleRepository;
use App\Repositories\Saving\SavingRepository;
use App\Repositories\SubCategory\SubCategoryRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Bridge\UserRepository;

class DashboardController extends Controller
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

    public function dashboardMember()
    {
        try {
            $user = Auth::user();

            $data_saving = [];

            $saving_members = $this->savingRepo->getSavingsMember($user->id);
            $sub_categories = $this->subCategoryRepo->getSubCategories();

            $filtered_sub_categories = filterSavingCategories($sub_categories);

            foreach ($filtered_sub_categories as $sub_category) {

                // simpanan
                $total = 0;
                foreach ($saving_members as $saving) {

                    if ($saving->sub_category_id == $sub_category->id) {
                        $total += $saving->amount;
                    }
                }

                $data_saving[$sub_category->name] = $total;
            }

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
            ];

            return response()->json([
                'data' => $data
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

}
