<?php

namespace App\Http\Controllers;

use App\Models\UserHealthInfo;
use App\Http\Requests\StoreUserHealthInfoRequest;
use App\Http\Requests\UpdateUserHealthInfoRequest;
use App\Http\Resources\UserHealthInfoResource;

class UserHealthInfoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user-health-infos",
     *     summary="Get all user health infos",
     *     tags={"User Health Info"},
     *     @OA\Response(
     *         response=200,
     *         description="List of user health infos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UserHealthInfoResource")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $userHealthInfos = UserHealthInfo::all();
        return UserHealthInfoResource::collection($userHealthInfos);
    }

    /**
     * @OA\Post(
     *     path="/api/user-health-infos",
     *     summary="Create a new user health info",
     *     tags={"User Health Info"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreUserHealthInfoRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User health info created",
     *         @OA\JsonContent(ref="#/components/schemas/UserHealthInfoResource")
     *     )
     * )
     */
    public function store(StoreUserHealthInfoRequest $request)
    {
        $userHealthInfo = UserHealthInfo::create($request->validated());
        return new UserHealthInfoResource($userHealthInfo);
    }

    /**
     * @OA\Get(
     *     path="/api/user-health-infos/{id}",
     *     summary="Get a specific user health info",
     *     tags={"User Health Info"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user health info",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User health info details",
     *         @OA\JsonContent(ref="#/components/schemas/UserHealthInfoResource")
     *     )
     * )
     */
    public function show(UserHealthInfo $userHealthInfo)
    {
        return new UserHealthInfoResource($userHealthInfo);
    }

    /**
     * @OA\Put(
     *     path="/api/user-health-infos/{id}",
     *     summary="Update a user health info",
     *     tags={"User Health Info"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user health info",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateUserHealthInfoRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User health info updated",
     *         @OA\JsonContent(ref="#/components/schemas/UserHealthInfoResource")
     *     )
     * )
     */
    public function update(UpdateUserHealthInfoRequest $request, UserHealthInfo $userHealthInfo)
    {
        $userHealthInfo->update($request->validated());
        return new UserHealthInfoResource($userHealthInfo);
    }

    /**
     * @OA\Delete(
     *     path="/api/user-health-infos/{id}",
     *     summary="Delete a user health info",
     *     tags={"User Health Info"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user health info",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="User health info deleted"
     *     )
     * )
     */
    public function destroy(UserHealthInfo $userHealthInfo)
    {
        $userHealthInfo->delete();
        return response()->noContent();
    }
}
