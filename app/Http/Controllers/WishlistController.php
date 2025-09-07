<?php

namespace App\Http\Controllers;

use App\Http\Resources\MediaResource;
use App\Models\Meal;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class WishlistController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/wishlist",
     *     summary="Get user's wishlist products with filtered media",
     *     tags={"Wishlist"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     )
     * )
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        /* @var Meal $meals */
        $meals = $user->wishlist()->get();

        return MediaResource::collection($meals->load('owner' , 'media'));
    }

    /**
     * @OA\Post(
     *     path="/api/wishlist",
     *     summary="Add meals to user's wishlist",
     *     description="Sync user's wishlist with provided meal IDs",
     *     operationId="wishlist.store",
     *     tags={"Wishlist"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="meal  IDs to add to wishlist",
     *         @OA\JsonContent(
     *             required={"mealId"},
     *             @OA\Property(
     *                 property="mealId",
     *                 type="array",
     *                 @OA\Items(
     *                     type="integer",
     *                     format="int64",
     *                     example=1
     *                 ),
     *                 description="Array of product IDs to add to wishlist"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Wishlist updated successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated - User not logged in"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error - Invalid product IDs"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'mealId' => 'required|integer|exists:meals,id'
        ]);

        /** @var User $user */
        $user = Auth::user();

        $user->wishlist()->toggle($request->input('mealId'));

        return response()->noContent();
    }
}
