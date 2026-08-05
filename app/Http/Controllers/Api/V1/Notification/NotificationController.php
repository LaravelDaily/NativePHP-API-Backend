<?php

namespace App\Http\Controllers\Api\V1\Notification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Notification\StoreNotificationRequest;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function store(StoreNotificationRequest $request): JsonResponse
    {
        $notification = Notification::query()->create($request->validated());

        return response()->json($notification, 201);
    }
}
