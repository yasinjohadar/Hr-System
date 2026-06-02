<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeApiController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load('employee');

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'employee_id' => $user->employee?->id,
            'roles' => $user->getRoleNames(),
        ]);
    }
}
