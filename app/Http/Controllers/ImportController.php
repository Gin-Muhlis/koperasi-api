<?php

namespace App\Http\Controllers;

require_once app_path() . "/Helpers/helpers.php";

use Exception;
use App\Imports\StuffImport;
use Illuminate\Http\Request;
use App\Imports\ProductImport;
use App\Imports\SubCategoryImport;
use App\Http\Requests\ImportRequest;
use App\Imports\MemberImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PositionCategoryImport;

class ImportController extends Controller
{
    public function importSubCategories(ImportRequest $request) {
        try {
            $validated = $request->validated();

            Excel::import(new SubCategoryImport, $validated['file']);

            return response()->json([
                'message' => 'Import data berhasil'
            ]);

        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function importProducts(ImportRequest $request) {
        try {
            $validated = $request->validated();

            Excel::import(new ProductImport, $validated['file']);

            return response()->json([
                'message' => 'Import data berhasil'
            ]);

        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function importStuffs(ImportRequest $request) {
        try {
            $validated = $request->validated();

            Excel::import(new StuffImport, $validated['file']);

            return response()->json([
                'message' => 'Import data berhasil'
            ]);

        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function importPositionCategories(ImportRequest $request) {
        try {
            $validated = $request->validated();

            Excel::import(new PositionCategoryImport, $validated['file']);

            return response()->json([
                'message' => 'Import data berhasil'
            ]);

        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function importMembers(ImportRequest $request) {
        try {
            $validated = $request->validated();

            Excel::import(new MemberImport, $validated['file']);

            return response()->json([
                'message' => 'Import data berhasil'
            ]);

        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }
}
