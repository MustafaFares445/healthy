<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrUpdateUserHealthInfoRequest;
use App\Http\Resources\UserHealthInfoResource;
use App\Models\UserHealthInfo;
use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class UserHealthInfoController extends Controller
{
    /**
     * Create or update user health information.
     *
     * @OA\Post(
     *     path="/api/user-health-info/create-or-update",
     *     summary="Create or update user health information",
     *     tags={"User Health Info"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"userId"},
     *             @OA\Property(property="userId", type="integer", example=1),
     *             @OA\Property(property="weight", type="number", format="float", example=70.5),
     *             @OA\Property(property="height", type="number", format="float", example=175.5),
     *             @OA\Property(property="activityLevel", type="string", enum={"sedentary", "active", "very_active"}, example="active"),
     *             @OA\Property(property="dietaryRestrictions", type="string", example="vegetarian"),
     *             @OA\Property(property="goal", type="string", enum={"weight_loss", "maintenance", "muscle_gain"}, example="weight_loss"),
     *             @OA\Property(property="healthNotes", type="string", example="No known allergies")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User health information updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User health information updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/UserHealthInfoResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User health information created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/UserHealthInfoResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function createOrUpdate(CreateOrUpdateUserHealthInfoRequest $request)
    {
        // Verify user exists
        $user = User::find($request->userId);
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // Prepare data for create/update
        $data = $request->only([
            'weight',
            'height', 
            'activityLevel',
            'dietaryRestrictions',
            'goal',
            'healthNotes'
        ]);
        $data['user_id'] = $request->userId;

        // Use updateOrCreate to handle both create and update
        $userHealthInfo = UserHealthInfo::updateOrCreate(
            ['user_id' => $request->userId],
            $data
        );

        $isCreated = $userHealthInfo->wasRecentlyCreated;
        $message = $isCreated ? 'User health information created successfully' : 'User health information updated successfully';
        $statusCode = $isCreated ? 201 : 200;

        return UserHealthInfoResource::make($userHealthInfo);
    }

    /**
     * Get user health information by user ID.
     *
     * @OA\Get(
     *     path="/api/user-health-info/{userId}",
     *     summary="Get user health information",
     *     tags={"User Health Info"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User health information retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/UserHealthInfoResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User health information not found"
     *     )
     * )
     */
    public function show($userId)
    {
        $userHealthInfo = UserHealthInfo::where('user_id', $userId)->first();
        
        if (!$userHealthInfo) {
            return response()->json([
                'message' => 'User health information not found'
            ], 404);
        }

        return response()->json([
            'data' => UserHealthInfoResource::make($userHealthInfo)
        ]);
    }
}
