<?php

namespace App\Http\Controllers;
use App\Http\Resources\MembersLoanReportResource;
use App\Http\Resources\MembersReportResource;
use App\Repositories\Role\RoleRepository;
use App\Repositories\SubCategory\SubCategoryRepository;

require_once app_path() . '/Helpers/helpers.php';

use App\Repositories\Installment\InstallmentRepository;
use App\Repositories\Invoice\InvoiceRepository;
use App\Repositories\Loan\LoanRepository;
use App\Repositories\Saving\SavingRepository;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Http\Resources\SavingMemberResource;
use App\Repositories\Member\MemberRepository;
use App\Repositories\User\UserRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MemberController extends Controller {
    private $memberRepo;
    private $userRepo;
    private $savingRepo;
    private $installmentRepo;
    private $invoiceRepo;
    private $loanRepo;
    private $roleRepo;
    private $subCategoryRepo;

    public function __construct( MemberRepository $member, UserRepository $user, SavingRepository $savingRepository, InstallmentRepository $installmentRepository, InvoiceRepository $invoiceRepository, LoanRepository $loanRepository, RoleRepository $roleRepository, SubCategoryRepository $subCategoryRepository ) {
        $this->memberRepo = $member;
        $this->userRepo = $user;
        $this->savingRepo = $savingRepository;
        $this->installmentRepo = $installmentRepository;
        $this->invoiceRepo = $invoiceRepository;
        $this->loanRepo = $loanRepository;
        $this->roleRepo = $roleRepository;
        $this->subCategoryRepo = $subCategoryRepository;
    }
    /**
    * Display a listing of the resource.
    */

    public function index( Request $request ) {
        try {
            $members = $this->memberRepo->getmembers();
            return response()->json( [
                'data' => MemberResource::collection( $members ),
            ] );
        } catch ( Exception $e ) {
            return errorResponse( $e->getMessage() );
        }
    }

    /**
    * Store a newly created resource in storage.
    */

    public function store( StoreMemberRequest $request ) {
        try {
            $validated = $request->validated();

            if ( $request->hasFile( 'image' ) ) {
                $validated[ 'image' ] = $request->file( 'image' )->store( 'public/member' );
            }

            $data_member = generateDataMember( 'store', null, $validated );

            DB::beginTransaction();

            $member = $this->memberRepo->createMember( $data_member );

            $data_user = generateDataUser( 'store', $member, $validated );

            $user = $this->userRepo->createUser( $data_user );

            $role = $this->roleRepo->searchRole( $validated[ 'role' ] );

            if ( is_null( $role ) ) {
                return response()->json( [
                    'message' => "Role {$validated['role']} tidak ditemukan"
                ], 400 );
            }

            $user->assignRole( $role->name );

            DB::commit();

            return response()->json( [
                'role' => $validated[ 'role' ],
                'message' => 'Data angggota berhasil ditambahkan',
            ] );
        } catch ( Exception $e ) {
            DB::rollBack();
            return errorResponse( $e->getMessage() );
        }
    }

    /**
    * Display the specified resource.
    */

    public function show( $id ) {
        try {
            $member = $this->memberRepo->showMember( $id );
            return response()->json( [
                'data' => new MemberResource( $member ),
            ] );
        } catch ( Exception $e ) {
            return errorResponse( $e->getMessage() );
        }
    }

    /**
    * Update the specified resource in storage.
    */

    public function update( UpdateMemberRequest $request, $id ) {
        try {
            $validated = $request->validated();

            $member = $this->memberRepo->showMember( $id );

            if ( $request->hasFile( 'image' ) ) {
                if ( $member->image ) {
                    Storage::delete( $member->image );
                }

                $validated[ 'image' ] = $request->file( 'image' )->store( 'public/member' );
            }

            $data_member = generateDataMember( 'update', $member, $validated );

            DB::beginTransaction();

            $this->memberRepo->updateMember( $id, $data_member );

            $data_user = generateDataUser( 'update', $member, $validated );

            $this->userRepo->updateUser( $member->user->id, $data_user );

			$role = $this->roleRepo->searchRole( $validated[ 'role' ] );

            if ( is_null( $role ) ) {
                return response()->json( [
                    'message' => "Role {$validated['role']} tidak ditemukan"
                ], 400 );
            }

            if ( $member->user->getRoleNames()->first() != $validated[ 'role' ] ) {
                $member->user->syncRoles( [ $validated[ 'role' ] ] );
            }

            DB::commit();

            return response()->json( [
                'message' => 'Data anggota berhasil diperbarui',
            ] );
        } catch ( Exception $e ) {
            DB::rollBack();
            return errorResponse( $e->getMessage() );
        }
    }

    /**
    * Remove the specified resource from storage.
    */

    public function destroy( $id ) {
        try {
            $this->memberRepo->deleteMember( $id );

            return response()->json( [
                'message' => 'Data member berhasil dihapus',
            ] );
        } catch ( Exception $e ) {
            return errorResponse( $e->getMessage() );
        }
    }

    public function reportMembers() {
        try {
            $sub_categories = $this->subCategoryRepo->getSubCategories();
			$members = $this->memberRepo->getMembers();

			$filtered_sub_categories = [];
			foreach ($sub_categories as $sub_category) {
				if ($sub_category->category->name == 'simpanan' || $sub_category->category->name == 'piutang') {
					$filtered_sub_categories[] = $sub_category;
				}
			}

			$members_data = $members->map(function($member) use ($filtered_sub_categories) {
				$data_dinamis = [];

				foreach ($filtered_sub_categories as $sub_category) {
					$detail = 0;

                    $total_saving = 0;
					// simpanan
					foreach ($member->savings as $saving) {
                        if ($saving->sub_category_id == $sub_category->id) {
                            $total_saving += $saving->amount;
                            $date = $saving->date;
                            $date_split = explode('-', $date);
							$detail = $total_saving;
						}
					}

                    $total_loan = 0;
					// pinjaman
					foreach ($member->loans as $loan) {
						if ($loan->sub_category_id == $sub_category->id) {
                            $total_loan += $loan->total_payment;
                            $date = $saving->date;
                            $date_split = explode('-', $date);
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

        } catch ( Exception $e ) {
            return errorResponse( $e->getMessage() );
        }
    }

    public function reportSavingMembers() {
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

        } catch ( Exception $e ) {
            return errorResponse( $e->getMessage() );
        }
    }

    public function reportLoanmembers() {
        try {
            $members = $this->memberRepo->getReportLoanMembers();

            $filtered_member = filterMember( $members );

            return response()->json( [
                'data' => MembersLoanReportResource::collection( $filtered_member ),
            ] );
        } catch ( Exception $e ) {
            return errorResponse( $e->getMessage() );
        }
    }

    

    public function dashboardMember() {
        try {
            $user = Auth::user();

            $total_mandatory_saving = 0;
            $total_special_mandatory_saving = 0;
            $total_voluntary_saving = 0;
            $total_recretional_saving = 0;

            $saving_members = $this->savingRepo->getSavingsMember( $user->id );

            foreach ( $saving_members as $saving ) {
                if ( $saving->subCategory->name = 'simpanan wajib' ) {
                    $total_mandatory_saving += $saving->amount;
                } else if ( $saving->subCategoryy->name = 'simpanan wajib khusus' ) {
                    $total_special_mandatory_saving += $saving->amount;
                } else if ( $saving->subCategoryy->name = 'simpanan sukarela' ) {
                    $total_voluntary_saving += $saving->amount;
                } else if ( $saving->subCategoryy->name = 'tabungan rekreasi' ) {
                    $total_recretional_saving += $saving->amount;
                }

            }

            $history_savings = $this->savingRepo->getHistorySavingmember( $user->id );
            $history_isntallments = $this->installmentRepo->getHistoryInstallments( $user->id );

            $result_saving = [];

            foreach ( $history_savings as $saving ) {
                $result_saving[] = [
                    ...$saving->toArray(),
                    'date' => $saving->date->toDateString(),
                ];
            }

            $data = [
                'total_mandatory_saving'=> $total_mandatory_saving,
                'total_special_mandatory_saving'=> $total_special_mandatory_saving,
                'total_voluntary_saving'=> $total_voluntary_saving,
                'total_recretional_saving'=> $total_recretional_saving,
                'history_savings' => $result_saving,
                'history_installments' => $history_isntallments
            ];

            return response()->json( [
                'data' => $data
            ] );
        } catch ( Exception $e ) {
            return errorResponse( $e->getMessage() );
        }
    }

    public function dashboardAdmin() {
        try {
            $count_members = $this->memberRepo->getCountMembers();
            $count_invoices_not_paid = $this->invoiceRepo->getNotPaidInvoices();
            $count_invoices_paid = $this->invoiceRepo->getPaidInvoices();
            $total_savings = $this->savingRepo->getTotalSavings();
            $total_loans = $this->loanRepo->getTotalLoans();
            $historyInvoices = $this->invoiceRepo->getHistoryInvoices();

            $data = [
                'count_member'=> $count_members,
                'count_invoices_not_paid'=> $count_invoices_not_paid,
                'count_invoices_paid'=> $count_invoices_paid,
                'total_savings' => $total_savings,
                'total_loans'=> $total_loans,
                'history_invoices'=> $historyInvoices
            ];

            return response()->json( [
                'data' => $data
            ] );
        } catch ( Exception $e ) {
            return errorResponse( $e->getMessage() );
        }
    }

}
