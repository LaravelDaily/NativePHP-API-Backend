<?php

namespace App\Http\Controllers\Api\V1\Cuesheet;

use App\Http\Controllers\Controller;
use App\Models\Cuesheet;
use Illuminate\Http\JsonResponse;

class CuesheetController extends Controller
{
    public function index(): JsonResponse
    {
        $cuesheets = Cuesheet::query()
            ->orderBy('id')
            ->get(['id', 'turn', 'notes', 'distance', 'created_at', 'updated_at']);

        return response()->json($cuesheets);
    }
}
