<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubCategoryResource;
use App\Models\Loan;
use App\Http\Requests\StoreLoanRequest;
use App\Http\Requests\UpdateLoanRequest;
use App\Repositories\SubCategory\SubCategoryRepository;
use Exception;

class LoanController extends Controller
{
    private $subCategoryRepo;

    public function __construct(SubCategoryRepository $subCategoryRepository)
    {
        $this->subCategoryRepo = $subCategoryRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLoanRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Loan $loan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLoanRequest $request, Loan $loan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Loan $loan)
    {
        //
    }
}
