<?php

namespace App\Http\Controllers;

use App\Models\SalesDetail;
use App\Http\Requests\StoreSalesDetailRequest;
use App\Http\Requests\UpdateSalesDetailRequest;

class SalesDetailController extends Controller
{
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
    public function store(StoreSalesDetailRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SalesDetail $salesDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSalesDetailRequest $request, SalesDetail $salesDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesDetail $salesDetail)
    {
        //
    }
}
