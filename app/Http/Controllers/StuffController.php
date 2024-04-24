<?php

namespace App\Http\Controllers;

require_once app_path() . '/Helpers/helpers.php';

use Exception;
use App\Models\Stuff;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\StuffResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreStuffRequest;
use App\Http\Requests\UpdateStuffRequest;
use App\Repositories\Stuff\StuffRepository;

class StuffController extends Controller
{
    private $stuffRepo;

    public function __construct(StuffRepository $stuffRepository) {
        $this->stuffRepo = $stuffRepository;
    }

    public function index()
    {
        try {
            $stuffs = $this->stuffRepo->getStuffs();

            return response()->json([
                'data' => StuffResource::collection($stuffs)
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function store(StoreStuffRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['uuid'] = Str::uuid();

            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('public/stuff');
            }

            DB::beginTransaction();

            $this->stuffRepo->createStuff($validated);

            DB::commit();

            return response()->json([
                'message' => 'Data barang berhasil ditambahkan'
            ]);
        } catch (Exception $e) {
        DB::rollBack();
            return errorResponse($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $stuff = $this->stuffRepo->showStuff($id);

            return response()->json([
                'data' => new StuffResource($stuff)
            ]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function update(UpdateStuffRequest $request, $id)
    {
        try {
            $validated = $request->validated();

            $stuff = $this->stuffRepo->showStuff($id);

            if ($request->hasFile('image')) {
                if ($stuff->image) {
                    Storage::delete($stuff->image);
                }
                $validated['image'] = $request->file('image')->store('public/stuff');
            }

            DB::beginTransaction();

            $this->stuffRepo->updateStuff($id, $validated);

            DB::commit();

            return response()->json([
                'message' => 'Data barang berhasil diperbarui'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return errorResponse($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $this->stuffRepo->deleteStuff($id);

            DB::commit();

            return response()->json([
                'message' => 'Data barang berhasil dihapus'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return errorResponse($e->getMessage());
        }
    }
}
