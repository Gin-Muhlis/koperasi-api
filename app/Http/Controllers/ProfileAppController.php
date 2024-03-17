<?php

namespace App\Http\Controllers;
use App\Http\Resources\ProfileAppResource;
use App\Repositories\ProfileApp\ProfileAppRepository;
use Exception;

require_once app_path() . '/Helpers/helpers.php';

use App\Models\ProfileApp;
use App\Http\Requests\StoreProfileAppRequest;
use App\Http\Requests\UpdateProfileAppRequest;

class ProfileAppController extends Controller
{
    private $profileRepo;

    public function __construct(ProfileAppRepository $profileAppRepository)
    {
        $this->profileRepo = $profileAppRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $profile = $this->profileRepo->getProfile();

            return response()->json([
                'data' => new ProfileAppResource($profile)
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProfileAppRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ProfileApp $profileApp)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProfileAppRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            $this->profileRepo->updateProfile($id, $validated);

            return response()->json([
                'message' => 'Data profile app berhasil diperbarui'
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProfileApp $profileApp)
    {
        //
    }
}
