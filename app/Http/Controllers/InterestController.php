<?php

namespace App\Http\Controllers;

use App\Models\Interest;
use App\Http\Requests\StoreInterestRequest;
use App\Http\Requests\UpdateInterestRequest;

class InterestController extends Controller
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
    public function store(StoreInterestRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Interest $interest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInterestRequest $request, Interest $interest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Interest $interest)
    {
        //
    }
}
