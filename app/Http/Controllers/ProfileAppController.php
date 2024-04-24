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
}
