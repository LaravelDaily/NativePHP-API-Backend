<?php

namespace App\Http\Controllers\Api\V1\Registration;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\JsonResponse;

class RegistrationController extends Controller
{
    public function index(): JsonResponse
    {
        $registrations = Registration::query()
            ->orderBy('id')
            ->get();

        return response()->json($registrations);
    }
}
