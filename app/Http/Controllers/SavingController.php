<?php

namespace App\Http\Controllers;

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

class SavingController extends Controller {
    private $savingRepository;
    private $memberRepository;
    private $subCategoryRepo;

    public function __construct( SavingRepository $savingRepository, MemberRepository $memberRepository, SubCategoryRepository $subCategoryRepository ) {
        $this->savingRepository = $savingRepository;
        $this->memberRepository = $memberRepository;
        $this->subCategoryRepo = $subCategoryRepository;
    }
    /**
    * Display a listing of the resource.
    */

    public function index() {
        //
    }

    /**
    * Store a newly created resource in storage.
    */

    public function store( StoreSavingRequest $request ) {
        try {
            $user = Auth::user();
            $validated = $request->validated();

            DB::beginTransaction();

            foreach ( $validated[ 'members' ] as $member ) {
                if ( $validated[ 'type_saving' ] != 'simpanan sukarela' && $validated[ 'type_saving' ] != 'tabungan rekreasi' ) {
                    $is_mandatory_saving = $this->savingRepository->getMemberSpesificSavings( $member[ 'id' ],  $validated[ 'sub_category_id' ] );

                    if ( count( $is_mandatory_saving ) > 0 && $is_mandatory_saving->contains( 'month_year', $validated[ 'month_year' ] ) ) {
                        return response()->json( [
                            'message' => 'Terdapat data member yang sudah membayar simpanan pada bulan yang ditentukan',
                        ], 400 );
                    }
                }

                $data = [
                    'uuid' => Str::uuid(),
                    'code' => generateCode(),
                    'member_id' => $member[ 'id' ],
                    'amount' => $member[ 'payment' ],
                    'date' => Carbon::now()->format( 'Y-m-d' ),
                    'sub_category_id' => $validated[ 'sub_category_id' ],
                    'month_year' => $validated[ 'month_year' ],
                    'user_id' => $user->id,
                    'description' => $validated[ 'description' ] ?? '-',
                    'status' => 'dibayar'
                ];

                $this->savingRepository->makeSavingMembers( $data );
            }

            DB::commit();

            return response()->json( [
                'success' => true,
                'message' => 'Simpanan member berhasil ditambahkan',
            ] );
        } catch ( Exception $e ) {
            DB::rollBack();

            return errorResponse( $e->getMessage() );
        }
    }

    /**
    * Display the specified resource.
    */

    public function show( Saving $saving ) {
        //
    }

    /**
    * Update the specified resource in storage.
    */

    public function update( UpdateSavingRequest $request, Saving $saving ) {
        //
    }

    /**
    * Remove the specified resource from storage.
    */

    public function destroy( Saving $saving ) {
        //
    }
}
